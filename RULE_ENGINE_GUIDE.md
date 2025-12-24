# Rule Engine Guide

This guide shows how to use the JSON rules, validate them, and execute them with the lightweight rule executor.

## Locations
- Rules: storage/app/private/rules/*.json
  - order_validation.json
  - discount_rules.json
  - vendor_restrictions.json
- Code: app/Services/RuleEngine/
  - RuleEngine.php (evaluate rulesets)
  - RuleLoader.php (loads JSON)
  - RuleValidator.php (basic schema checks)

## JSON schema (light)
Each ruleset file:
```json
{
  "name": "order_validation",
  "description": "...",
  "rules": [
    {
      "id": "cod_min_amount",
      "when": [
        { "field": "payment.method", "operator": "equals", "value": "cod" },
        { "field": "order.total", "operator": "lt", "value": 500 }
      ],
      "then": { "action": "deny", "reason": "Cash on delivery is available for orders >= 500." }
    }
  ]
}
```

Supported operators: equals, not_equals, in, not_in, gt, gte, lt, lte, between, exists, missing.

## Sample contexts
Provide a flat array with dotted keys resolved by the executor:
```php
$context = [
    'order' => [
        'total' => 620,
        'day_of_week' => 6,
        'out_of_stock_count' => 0,
        'has_digital_items' => false,
    ],
    'payment' => ['method' => 'cod'],
    'customer' => ['is_first_order' => true],
    'vendor' => [
        'id' => 101,
        'status' => 'approved',
        'kyc_status' => 'approved',
        'payout_hold' => false,
        'dispute_rate' => 0.02,
    ],
];
```

## Executing
```php
use App\Services\RuleEngine\RuleEngine;

$result = RuleEngine::evaluate('order_validation', $context);
// $result['denies'], $result['discounts'], $result['applied'], $result['trace']
```

## Error handling
- Invalid JSON throws a RuntimeException from RuleLoader.
- Schema violations throw a RuntimeException with collected errors from RuleValidator.
- Execution traces are returned per rule and logged via Log::info for clarity.

## Real-world examples included
- order_validation.json: COD minimum, vendor pending/blocked gate, inventory gaps, digital items no COD.
- discount_rules.json: first-order bonus, featured vendor promo, weekend push.
- vendor_restrictions.json: KYC pending, payout hold, high dispute rate gate.

## Extending safely
- Add new rules by appending to the relevant JSON file; keep ids unique.
- Stick to supported operators; add new operators in RuleEngine::evaluateCondition if needed.
- Avoid moving files/directories to keep the current structure intact.

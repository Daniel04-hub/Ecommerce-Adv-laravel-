<?php

namespace App\Services\RuleEngine;

use Illuminate\Support\Facades\Log;
use RuntimeException;

class RuleEngine
{
    public static function evaluate(string $rulesetName, array $context): array
    {
        $ruleset = RuleLoader::load($rulesetName);
        $errors = RuleValidator::validate($ruleset);

        if ($errors) {
            throw new RuntimeException('Invalid ruleset: ' . implode('; ', $errors));
        }

        $result = [
            'ruleset'   => $ruleset['name'] ?? $rulesetName,
            'applied'   => [],
            'denies'    => [],
            'discounts' => [],
            'skipped'   => [],
            'trace'     => [],
        ];

        foreach ($ruleset['rules'] as $rule) {
            [$matched, $trace] = self::evaluateRule($rule, $context);
            $result['trace'][$rule['id']] = $trace;

            if (!$matched) {
                $result['skipped'][] = $rule['id'];
                continue;
            }

            $action = $rule['then']['action'];

            if ($action === 'deny') {
                $result['denies'][] = [
                    'rule_id' => $rule['id'],
                    'reason'  => $rule['then']['reason'] ?? 'Blocked by rule',
                ];
            }

            if ($action === 'apply_discount') {
                $result['discounts'][] = [
                    'rule_id' => $rule['id'],
                    'value'   => $rule['then']['value'] ?? 0,
                    'unit'    => $rule['then']['unit'] ?? 'percent',
                    'label'   => $rule['then']['label'] ?? 'Discount',
                ];
            }

            $result['applied'][] = $rule['id'];
        }

        Log::info('Rule engine evaluation', [
            'ruleset'   => $rulesetName,
            'applied'   => $result['applied'],
            'denies'    => $result['denies'],
            'discounts' => $result['discounts'],
        ]);

        return $result;
    }

    private static function evaluateRule(array $rule, array $context): array
    {
        $trace = [];

        foreach ($rule['when'] as $condition) {
            $passed = self::evaluateCondition($condition, $context);
            $trace[] = [
                'field'    => $condition['field'] ?? null,
                'operator' => $condition['operator'] ?? null,
                'value'    => $condition['value'] ?? null,
                'passed'   => $passed,
            ];

            if (!$passed) {
                return [false, $trace];
            }
        }

        return [true, $trace];
    }

    private static function evaluateCondition(array $condition, array $context): bool
    {
        $field = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? 'equals';
        $expected = $condition['value'] ?? null;
        $actual = self::getValue($context, $field);

        switch ($operator) {
            case 'equals':
                return $actual === $expected;
            case 'not_equals':
                return $actual !== $expected;
            case 'in':
                return is_array($expected) && in_array($actual, $expected, true);
            case 'not_in':
                return is_array($expected) && !in_array($actual, $expected, true);
            case 'gt':
                return is_numeric($actual) && $actual > $expected;
            case 'gte':
                return is_numeric($actual) && $actual >= $expected;
            case 'lt':
                return is_numeric($actual) && $actual < $expected;
            case 'lte':
                return is_numeric($actual) && $actual <= $expected;
            case 'between':
                return is_array($expected)
                    && count($expected) === 2
                    && is_numeric($actual)
                    && $actual >= $expected[0]
                    && $actual <= $expected[1];
            case 'exists':
                return $actual !== null;
            case 'missing':
                return $actual === null;
            default:
                return false;
        }
    }

    private static function getValue(array $context, ?string $path)
    {
        if ($path === null || $path === '') {
            return null;
        }

        $segments = explode('.', $path);
        $value = $context;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return null;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}

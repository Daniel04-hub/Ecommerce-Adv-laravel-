<?php

namespace App\Services\RuleEngine;

class RuleValidator
{
    public static function validate(array $ruleset): array
    {
        $errors = [];

        if (!isset($ruleset['name']) || !is_string($ruleset['name'])) {
            $errors[] = 'ruleset.name must be a string';
        }

        if (!isset($ruleset['rules']) || !is_array($ruleset['rules'])) {
            $errors[] = 'ruleset.rules must be an array';
            return $errors;
        }

        foreach ($ruleset['rules'] as $idx => $rule) {
            if (!isset($rule['id']) || !is_string($rule['id'])) {
                $errors[] = "rules[{$idx}].id must be a string";
            }

            if (!isset($rule['when']) || !is_array($rule['when']) || $rule['when'] === []) {
                $errors[] = "rules[{$idx}].when must be a non-empty array";
            }

            if (!isset($rule['then']) || !is_array($rule['then'])) {
                $errors[] = "rules[{$idx}].then must be an object";
            } else {
                if (!isset($rule['then']['action']) || !is_string($rule['then']['action'])) {
                    $errors[] = "rules[{$idx}].then.action must be a string";
                }
            }
        }

        return $errors;
    }
}

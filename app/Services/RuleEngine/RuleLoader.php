<?php

namespace App\Services\RuleEngine;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

class RuleLoader
{
    public static function load(string $name): array
    {
        $path = "private/rules/{$name}.json";

        if (!Storage::disk('local')->exists($path)) {
            throw new RuntimeException("Ruleset {$name} not found at {$path}");
        }

        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Ruleset {$name} JSON error: " . json_last_error_msg());
        }

        return $data;
    }
}

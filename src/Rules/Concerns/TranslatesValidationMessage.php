<?php

namespace Trishanapp\LkBusinessValidator\Rules\Concerns;

trait TranslatesValidationMessage
{
    /**
     * Resolve a localized validation message via Laravel's translator when available,
     * falling back to the default English message.
     *
     * @param array<string, string|int> $replace
     */
    protected function message(string $key, string $fallback, array $replace = []): string
    {
        $fullKey = 'lk-validator::validation.' . $key;

        if (function_exists('__')) {
            $translated = __($fullKey, $replace);
            if ($translated !== $fullKey) {
                return (string) $translated;
            }
        }

        foreach ($replace as $placeholder => $value) {
            $fallback = str_replace(':' . $placeholder, (string) $value, $fallback);
        }

        return $fallback;
    }
}

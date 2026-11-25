<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka Passport Number Validator (Department of Immigration & Emigration)
 *
 * Supported passport series:
 *  - N + 7 digits:  Ordinary Passport (all countries)
 *  - M + 7 digits:  Ordinary Passport (Middle East / legacy series)
 *  - OL + 7 digits: Official Passport
 *  - D + 7 digits:  Diplomatic Passport
 *  - E + 7 digits:  Emergency Certificate
 */
class PassportValidator
{
    public const SERIES = [
        'N'  => 'Ordinary Passport',
        'M'  => 'Ordinary Passport (M Series)',
        'OL' => 'Official Passport',
        'D'  => 'Diplomatic Passport',
        'E'  => 'Emergency Certificate',
    ];

    /**
     * Validate a Sri Lanka passport number.
     */
    public static function validate(string $passport): bool
    {
        $cleaned = self::clean($passport);

        return (bool) preg_match('/^(OL|N|M|D|E)\d{7}$/', $cleaned);
    }

    /**
     * Normalize a valid passport number (uppercase, no spaces/hyphens).
     */
    public static function normalize(string $passport): ?string
    {
        $cleaned = self::clean($passport);

        return self::validate($cleaned) ? $cleaned : null;
    }

    /**
     * Get the passport type from a valid passport number.
     */
    public static function getType(string $passport): ?string
    {
        $normalized = self::normalize($passport);
        if ($normalized === null) {
            return null;
        }

        preg_match('/^(OL|N|M|D|E)/', $normalized, $m);

        return self::SERIES[$m[1]] ?? null;
    }

    /**
     * Parse passport number into details.
     *
     * @return array{valid: bool, normalized: string|null, series: string|null, type: string|null, number: string|null}
     */
    public static function parse(string $passport): array
    {
        $normalized = self::normalize($passport);

        if ($normalized === null) {
            return [
                'valid'      => false,
                'normalized' => null,
                'series'     => null,
                'type'       => null,
                'number'     => null,
            ];
        }

        preg_match('/^(OL|N|M|D|E)(\d{7})$/', $normalized, $m);

        return [
            'valid'      => true,
            'normalized' => $normalized,
            'series'     => $m[1],
            'type'       => self::SERIES[$m[1]],
            'number'     => $m[2],
        ];
    }

    private static function clean(string $passport): string
    {
        return strtoupper((string) preg_replace('/[\s\-]+/', '', trim($passport)));
    }
}

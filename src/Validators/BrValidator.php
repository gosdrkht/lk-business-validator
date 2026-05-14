<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka Business Registration (BR) Number Validator
 *
 * Formats supported:
 *  - PV/PB/HP/SP/PR followed by digits  (e.g. PV00123456)
 *  - Department of Registrar of Companies format
 */
class BrValidator
{
    const PREFIXES = ['PV', 'PB', 'HP', 'SP', 'PR', 'GN', 'SC'];

    /**
     * Validate a Sri Lanka Business Registration number.
     */
    public static function validate(string $br): bool
    {
        $br = strtoupper(trim($br));

        // Format: PREFIX + optional slash + digits (5–8 digits)
        // Examples: PV00123456, PV/00123456, PB12345
        foreach (self::PREFIXES as $prefix) {
            if (preg_match('/^' . $prefix . '\/?(\d{5,8})$/', $br)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize BR number (remove slashes, uppercase).
     */
    public static function normalize(string $br): ?string
    {
        $br = strtoupper(trim($br));
        $br = str_replace('/', '', $br);

        if (!self::validate($br)) {
            return null;
        }

        return $br;
    }

    /**
     * Get the company type from BR prefix.
     */
    public static function getCompanyType(string $br): ?string
    {
        $br = strtoupper(trim($br));

        $types = [
            'PV' => 'Private Limited Company',
            'PB' => 'Public Company',
            'HP' => 'Foreign Company (Branch)',
            'SP' => 'Sole Proprietorship',
            'PR' => 'Partnership',
            'GN' => 'Guarantee Company',
            'SC' => 'Unlimited Company',
        ];

        foreach ($types as $prefix => $type) {
            if (str_starts_with($br, $prefix)) {
                return $type;
            }
        }

        return null;
    }

    /**
     * Parse BR number into details.
     *
     * @return array{valid: bool, normalized: string|null, prefix: string|null, company_type: string|null}
     */
    public static function parse(string $br): array
    {
        $normalized = self::normalize($br);

        if (!$normalized) {
            return ['valid' => false, 'normalized' => null, 'prefix' => null, 'company_type' => null];
        }

        preg_match('/^([A-Z]+)/', $normalized, $m);

        return [
            'valid'        => true,
            'normalized'   => $normalized,
            'prefix'       => $m[1] ?? null,
            'company_type' => self::getCompanyType($normalized),
        ];
    }
}

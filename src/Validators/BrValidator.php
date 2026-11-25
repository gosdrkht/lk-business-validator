<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka Business Registration (BR) Number Validator
 *
 * Supports Department of Registrar of Companies (DRC) and business registration prefixes:
 *  PV, PB, HP, FC, SP, PR, GN, GA, GL, SC, CS, NA
 *  with optional '/', '-', or space separators (e.g. PV00123456, PV/00123456, PV-12345).
 */
class BrValidator
{
    public const TYPES = [
        'PV' => 'Private Limited Company',
        'PB' => 'Public Company',
        'HP' => 'Foreign Company (Branch)',
        'FC' => 'Overseas Company',
        'SP' => 'Sole Proprietorship',
        'PR' => 'Partnership',
        'GN' => 'Guarantee Company',
        'GA' => 'Guarantee Company',
        'GL' => 'Company Limited by Guarantee',
        'SC' => 'Unlimited Company',
        'CS' => 'Consumer Society',
        'NA' => 'Non-Profit Association',
    ];

    public const PREFIXES = ['PV', 'PB', 'HP', 'FC', 'SP', 'PR', 'GN', 'GA', 'GL', 'SC', 'CS', 'NA'];

    /**
     * Validate a Sri Lanka Business Registration number.
     */
    public static function validate(string $br): bool
    {
        $br = strtoupper(trim($br));

        $prefixes = implode('|', self::PREFIXES);

        return (bool) preg_match('/^(' . $prefixes . ')[\s\/\-]?(\d{5,8})$/', $br);
    }

    /**
     * Normalize BR number (uppercase, remove separators).
     */
    public static function normalize(string $br): ?string
    {
        $br = strtoupper(trim($br));
        if (!self::validate($br)) {
            return null;
        }

        return preg_replace('/[\s\/\-]+/', '', $br);
    }

    /**
     * Get the company type from a valid BR number (or prefix).
     */
    public static function getCompanyType(string $br): ?string
    {
        $br = strtoupper(trim($br));

        foreach (self::TYPES as $prefix => $type) {
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

        if ($normalized === null) {
            return [
                'valid'        => false,
                'normalized'   => null,
                'prefix'       => null,
                'company_type' => null,
            ];
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

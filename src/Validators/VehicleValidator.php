<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka Vehicle Registration Number Validator (Department of Motor Traffic)
 *
 * Supports:
 *  - Modern provincial format: WP CAB-1234, CP KA-1234, SP CAE 5678
 *  - Modern without province:  CAB-1234, KA-1234, CAE1234
 *  - Old numeric format:       250-1234, 19-1234, 300-4567
 *  - Vintage Sri format:       1 SRI 1234, 12 ශ්‍රී 1234
 */
class VehicleValidator
{
    /** @var array<string, string> Provincial prefix => Province name */
    public const PROVINCES = [
        'WP' => 'Western',
        'CP' => 'Central',
        'SP' => 'Southern',
        'NW' => 'North Western',
        'NC' => 'North Central',
        'SG' => 'Sabaragamuwa',
        'UV' => 'Uva',
        'EP' => 'Eastern',
        'NP' => 'Northern',
    ];

    /**
     * Validate a Sri Lanka vehicle registration number.
     */
    public static function validate(string $plate): bool
    {
        return self::parseInternal($plate) !== null;
    }

    /**
     * Normalize a valid vehicle number (e.g. 'WP CAB-1234', 'CAB-1234', '250-1234', '12 SRI 1234').
     */
    public static function normalize(string $plate): ?string
    {
        return self::parseInternal($plate)['normalized'] ?? null;
    }

    /**
     * Extract province name if a provincial prefix is present.
     */
    public static function getProvince(string $plate): ?string
    {
        return self::parseInternal($plate)['province'] ?? null;
    }

    /**
     * Get the registration format ('modern', 'numeric', or 'vintage').
     */
    public static function getFormat(string $plate): ?string
    {
        return self::parseInternal($plate)['format'] ?? null;
    }

    /**
     * Parse vehicle registration number into full details.
     *
     * @return array{
     *     valid: bool,
     *     normalized: string|null,
     *     format: string|null,
     *     province_code: string|null,
     *     province: string|null,
     *     prefix: string|null,
     *     number: string|null
     * }
     */
    public static function parse(string $plate): array
    {
        $parsed = self::parseInternal($plate);

        if ($parsed === null) {
            return [
                'valid'         => false,
                'normalized'    => null,
                'format'        => null,
                'province_code' => null,
                'province'      => null,
                'prefix'        => null,
                'number'        => null,
            ];
        }

        return [
            'valid'         => true,
            'normalized'    => $parsed['normalized'],
            'format'        => $parsed['format'],
            'province_code' => $parsed['province_code'],
            'province'      => $parsed['province'],
            'prefix'        => $parsed['prefix'],
            'number'        => $parsed['number'],
        ];
    }

    /**
     * @return array{
     *     normalized: string,
     *     format: string,
     *     province_code: string|null,
     *     province: string|null,
     *     prefix: string,
     *     number: string
     * }|null
     */
    private static function parseInternal(string $plate): ?array
    {
        $cleaned = trim(preg_replace('/\s+/', ' ', $plate) ?? '');
        if ($cleaned === '') {
            return null;
        }

        // Vintage Sri format: 1–2 digits + SRI / ශ්‍රී + 4 digits (e.g. 12 SRI 1234)
        if (preg_match('/^(\d{1,2})[\s\-]?(?:SRI|ශ්‍රී)[\s\-]?(\d{4})$/iu', $cleaned, $m)) {
            if ($m[2] === '0000') {
                return null;
            }

            return [
                'normalized'    => $m[1] . ' SRI ' . $m[2],
                'format'        => 'vintage',
                'province_code' => null,
                'province'      => null,
                'prefix'        => $m[1] . ' SRI',
                'number'        => $m[2],
            ];
        }

        $upper = strtoupper($cleaned);
        $provinces = implode('|', array_keys(self::PROVINCES));

        // Modern format with province: WP CAB-1234, CP KA 1234, SP CAE1234
        if (preg_match('/^(' . $provinces . ')[\s\-]+([A-Z]{2,3})[\s\-]?(\d{4})$/', $upper, $m)) {
            if ($m[3] === '0000') {
                return null;
            }

            return [
                'normalized'    => $m[1] . ' ' . $m[2] . '-' . $m[3],
                'format'        => 'modern',
                'province_code' => $m[1],
                'province'      => self::PROVINCES[$m[1]],
                'prefix'        => $m[2],
                'number'        => $m[3],
            ];
        }

        // Modern format without province: CAB-1234, KA-1234, CAE 5678
        if (preg_match('/^([A-Z]{2,3})[\s\-]?(\d{4})$/', $upper, $m)) {
            if ($m[2] === '0000') {
                return null;
            }

            return [
                'normalized'    => $m[1] . '-' . $m[2],
                'format'        => 'modern',
                'province_code' => null,
                'province'      => null,
                'prefix'        => $m[1],
                'number'        => $m[2],
            ];
        }

        // Old numeric format: 2–3 digits + 4 digits (e.g. 250-1234, 19-1234)
        if (preg_match('/^(\d{2,3})[\s\-](\d{4})$/', $upper, $m)) {
            if ($m[2] === '0000') {
                return null;
            }

            return [
                'normalized'    => $m[1] . '-' . $m[2],
                'format'        => 'numeric',
                'province_code' => null,
                'province'      => null,
                'prefix'        => $m[1],
                'number'        => $m[2],
            ];
        }

        return null;
    }
}

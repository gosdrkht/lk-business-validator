<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka Postal Code Validator
 *
 * All valid Sri Lanka postal codes are 5-digit numbers.
 * Covers all provinces and districts.
 */
class PostalCodeValidator
{
    /**
     * Known valid postal code ranges by province.
     * Format: [province => [district => [start, end]]]
     */
    const RANGES = [
        'Western'       => ['Colombo' => [1, 15], 'Gampaha' => [11000, 11600], 'Kalutara' => [12000, 12750]],
        'Central'       => ['Kandy' => [20000, 20800], 'Matale' => [21000, 21500], 'NuwaraEliya' => [22000, 22300]],
        'Southern'      => ['Galle' => [80000, 80650], 'Matara' => [81000, 81600], 'Hambantota' => [82000, 82500]],
        'Northern'      => ['Jaffna' => [40000, 40600], 'Kilinochchi' => [44000, 44100], 'Mullaitivu' => [43000, 43100], 'Vavuniya' => [43000, 43300], 'Mannar' => [41000, 41100]],
        'Eastern'       => ['Trincomalee' => [31000, 31200], 'Batticaloa' => [30000, 30200], 'Ampara' => [32000, 32600]],
        'NorthWestern'  => ['Kurunegala' => [60000, 60800], 'Puttalam' => [61000, 61600]],
        'NorthCentral'  => ['Anuradhapura' => [50000, 50450], 'Polonnaruwa' => [51000, 51400]],
        'Uva'           => ['Badulla' => [90000, 90600], 'Monaragala' => [91000, 91600]],
        'Sabaragamuwa'  => ['Ratnapura' => [70000, 70600], 'Kegalle' => [71000, 71200]],
    ];

    /**
     * Validate a Sri Lanka postal code.
     */
    public static function validate(string|int $code): bool
    {
        $code = (string) $code;
        $code = trim($code);

        // Must be exactly 5 digits
        if (!preg_match('/^\d{5}$/', $code)) {
            return false;
        }

        $num = (int) $code;

        // Colombo city codes: 00100–01500 (written as 1–15 with leading zeros)
        if ($num >= 100 && $num <= 1500) return true;

        // All others: 10000–99999
        return $num >= 10000 && $num <= 99999;
    }

    /**
     * Get province for a postal code (best effort).
     */
    public static function getProvince(string|int $code): ?string
    {
        $num = (int) $code;

        $map = [
            [1,       1500,  'Western'],
            [10000,   15000, 'Western'],
            [20000,   22999, 'Central'],
            [30000,   32999, 'Eastern'],
            [40000,   44999, 'Northern'],
            [50000,   51999, 'North Central'],
            [60000,   61999, 'North Western'],
            [70000,   71999, 'Sabaragamuwa'],
            [80000,   82999, 'Southern'],
            [90000,   91999, 'Uva'],
        ];

        foreach ($map as [$start, $end, $province]) {
            if ($num >= $start && $num <= $end) {
                return $province;
            }
        }

        return null;
    }

    /**
     * Parse postal code.
     *
     * @return array{valid: bool, code: string|null, province: string|null}
     */
    public static function parse(string|int $code): array
    {
        $code = trim((string) $code);

        if (!self::validate($code)) {
            return ['valid' => false, 'code' => null, 'province' => null];
        }

        return [
            'valid'    => true,
            'code'     => str_pad($code, 5, '0', STR_PAD_LEFT),
            'province' => self::getProvince($code),
        ];
    }
}

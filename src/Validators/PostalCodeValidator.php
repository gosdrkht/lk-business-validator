<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka Postal Code Validator
 *
 * Validates 5-digit Sri Lanka postal codes against real Sri Lanka Post
 * province and district ranges.
 */
class PostalCodeValidator
{
    /**
     * Valid Sri Lanka postal code ranges by province and district.
     * Format: [province => [district => list of [start, end] ranges]]
     *
     * @var array<string, array<string, list<array{0: int, 1: int}>>>
     */
    public const RANGES = [
        'Western' => [
            'Colombo'  => [[100, 1500], [10000, 10799]],
            'Gampaha'  => [[11000, 11899]],
            'Kalutara' => [[12000, 12799]],
        ],
        'Central' => [
            'Kandy'        => [[20000, 20999]],
            'Matale'       => [[21000, 21599]],
            'Nuwara Eliya' => [[22000, 22399]],
        ],
        'Eastern' => [
            'Batticaloa'  => [[30000, 30499]],
            'Trincomalee' => [[31000, 31499]],
            'Ampara'      => [[32000, 32699]],
        ],
        'Northern' => [
            'Jaffna'      => [[40000, 40699]],
            'Mannar'      => [[41000, 41299]],
            'Mullaitivu'  => [[42000, 42199]],
            'Vavuniya'    => [[43000, 43399]],
            'Kilinochchi' => [[44000, 44199]],
        ],
        'North Central' => [
            'Anuradhapura' => [[50000, 50599]],
            'Polonnaruwa'  => [[51000, 51499]],
        ],
        'North Western' => [
            'Kurunegala' => [[60000, 60899]],
            'Puttalam'   => [[61000, 61699]],
        ],
        'Sabaragamuwa' => [
            'Ratnapura' => [[70000, 70799]],
            'Kegalle'   => [[71000, 71799]],
        ],
        'Southern' => [
            'Galle'      => [[80000, 80699]],
            'Matara'     => [[81000, 81799]],
            'Hambantota' => [[82000, 82699]],
        ],
        'Uva' => [
            'Badulla'    => [[90000, 90699]],
            'Monaragala' => [[91000, 91699]],
        ],
    ];

    /**
     * Validate a Sri Lanka postal code against real Sri Lanka Post ranges.
     */
    public static function validate(string|int $code): bool
    {
        return self::lookup($code) !== null;
    }

    /**
     * Get province for a valid postal code.
     */
    public static function getProvince(string|int $code): ?string
    {
        return self::lookup($code)['province'] ?? null;
    }

    /**
     * Get district for a valid postal code.
     */
    public static function getDistrict(string|int $code): ?string
    {
        return self::lookup($code)['district'] ?? null;
    }

    /**
     * Parse postal code into full details.
     *
     * @return array{valid: bool, code: string|null, province: string|null, district: string|null}
     */
    public static function parse(string|int $code): array
    {
        $match = self::lookup($code);

        if ($match === null) {
            return [
                'valid'    => false,
                'code'     => null,
                'province' => null,
                'district' => null,
            ];
        }

        return [
            'valid'    => true,
            'code'     => $match['code'],
            'province' => $match['province'],
            'district' => $match['district'],
        ];
    }

    /**
     * @return array{code: string, province: string, district: string}|null
     */
    private static function lookup(string|int $code): ?array
    {
        if (is_int($code)) {
            if ($code < 100 || $code > 99999) {
                return null;
            }
            $normalized = str_pad((string) $code, 5, '0', STR_PAD_LEFT);
        } else {
            $normalized = trim($code);
            if (!preg_match('/^\d{5}$/', $normalized)) {
                return null;
            }
        }

        $num = (int) $normalized;

        foreach (self::RANGES as $province => $districts) {
            foreach ($districts as $district => $ranges) {
                foreach ($ranges as [$start, $end]) {
                    if ($num >= $start && $num <= $end) {
                        return [
                            'code'     => $normalized,
                            'province' => $province,
                            'district' => $district,
                        ];
                    }
                }
            }
        }

        return null;
    }
}

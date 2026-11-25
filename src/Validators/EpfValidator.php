<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka EPF / ETF Number Validator (Employees' Provident Fund / Employees' Trust Fund)
 *
 * Formats:
 *  - Employer Number: Zone letter (A–Z) + optional '/' + 4–6 digits (e.g. C/12345, A/123456, C12345)
 *  - Member Number:   1–6 digits (e.g. 1234, 001234)
 *  - Combined:        Zone/Employer/Member (e.g. C/12345/001234)
 */
class EpfValidator
{
    /**
     * Validate a Sri Lanka EPF/ETF employer number (e.g. C/12345, A/123456, C12345).
     */
    public static function validateEmployerNumber(string $employerNumber): bool
    {
        $cleaned = strtoupper(trim($employerNumber));

        return (bool) preg_match('/^[A-Z][\s\/\-]?\d{4,6}$/', $cleaned);
    }

    /**
     * Validate an EPF member number (1–6 digits, not all zeros).
     */
    public static function validateMemberNumber(string|int $memberNumber): bool
    {
        $cleaned = trim((string) $memberNumber);

        return (bool) preg_match('/^\d{1,6}$/', $cleaned) && (int) $cleaned > 0;
    }

    /**
     * Validate an EPF employer number or combined employer/member number (e.g. C/12345 or C/12345/001234).
     */
    public static function validate(string $value): bool
    {
        $cleaned = strtoupper(trim($value));

        if (self::validateEmployerNumber($cleaned)) {
            return true;
        }

        if (preg_match('/^([A-Z])\/(\d{4,6})\/(\d{1,6})$/', $cleaned, $m)) {
            return (int) $m[3] > 0;
        }

        return false;
    }

    /**
     * Normalize an EPF employer number to standard 'ZONE/NUMBER' format (e.g. 'C/12345').
     */
    public static function normalizeEmployerNumber(string $employerNumber): ?string
    {
        $cleaned = strtoupper(trim($employerNumber));

        if (!preg_match('/^([A-Z])[\s\/\-]?(\d{4,6})$/', $cleaned, $m)) {
            return null;
        }

        return $m[1] . '/' . $m[2];
    }

    /**
     * Parse an EPF employer or combined employer/member number.
     *
     * @return array{
     *     valid: bool,
     *     normalized: string|null,
     *     zone: string|null,
     *     employer_number: string|null,
     *     member_number: string|null
     * }
     */
    public static function parse(string $value): array
    {
        $cleaned = strtoupper(trim($value));

        if (preg_match('/^([A-Z])\/(\d{4,6})\/(\d{1,6})$/', $cleaned, $m) && (int) $m[3] > 0) {
            return [
                'valid'           => true,
                'normalized'      => $m[1] . '/' . $m[2] . '/' . $m[3],
                'zone'            => $m[1],
                'employer_number' => $m[1] . '/' . $m[2],
                'member_number'   => $m[3],
            ];
        }

        $employer = self::normalizeEmployerNumber($cleaned);
        if ($employer !== null) {
            [$zone] = explode('/', $employer);

            return [
                'valid'           => true,
                'normalized'      => $employer,
                'zone'            => $zone,
                'employer_number' => $employer,
                'member_number'   => null,
            ];
        }

        return [
            'valid'           => false,
            'normalized'      => null,
            'zone'            => null,
            'employer_number' => null,
            'member_number'   => null,
        ];
    }
}

<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka National Identity Card (NIC) Validator
 *
 * Supports:
 *  - Old format: 9 digits + V/X  (e.g. 851234567V)
 *  - New format: 12 digits        (e.g. 198512345678)
 */
class NicValidator
{
    /**
     * Validate a NIC number.
     */
    public static function validate(string $nic): bool
    {
        $nic = strtoupper(trim($nic));

        return self::isOldFormat($nic) || self::isNewFormat($nic);
    }

    /**
     * Check if old format (9 digits + V or X).
     */
    public static function isOldFormat(string $nic): bool
    {
        return (bool) preg_match('/^\d{9}[VX]$/', strtoupper(trim($nic)));
    }

    /**
     * Check if new format (12 digits).
     */
    public static function isNewFormat(string $nic): bool
    {
        return (bool) preg_match('/^\d{12}$/', trim($nic));
    }

    /**
     * Extract birth year from NIC.
     */
    public static function getBirthYear(string $nic): ?int
    {
        $nic = strtoupper(trim($nic));

        if (self::isOldFormat($nic)) {
            // Old format: first 2 digits = year (19xx)
            return (int)('19' . substr($nic, 0, 2));
        }

        if (self::isNewFormat($nic)) {
            // New format: first 4 digits = full year
            return (int) substr($nic, 0, 4);
        }

        return null;
    }

    /**
     * Extract day of year from NIC.
     * For females, 500 is added to the day value.
     */
    public static function getDayOfYear(string $nic): ?int
    {
        $nic = strtoupper(trim($nic));

        if (self::isOldFormat($nic)) {
            $day = (int) substr($nic, 2, 3);
        } elseif (self::isNewFormat($nic)) {
            $day = (int) substr($nic, 4, 3);
        } else {
            return null;
        }

        // Female NICs have 500 added
        return $day > 500 ? $day - 500 : $day;
    }

    /**
     * Determine gender from NIC.
     */
    public static function getGender(string $nic): ?string
    {
        $nic = strtoupper(trim($nic));

        if (self::isOldFormat($nic)) {
            $day = (int) substr($nic, 2, 3);
        } elseif (self::isNewFormat($nic)) {
            $day = (int) substr($nic, 4, 3);
        } else {
            return null;
        }

        return $day > 500 ? 'Female' : 'Male';
    }

    /**
     * Convert old NIC format to new 12-digit format.
     */
    public static function convertToNew(string $nic): ?string
    {
        $nic = strtoupper(trim($nic));

        if (!self::isOldFormat($nic)) {
            return null;
        }

        $year = '19' . substr($nic, 0, 2);
        $rest = substr($nic, 2, 7); // 7 digits: day-of-year (3) + serial (4)

        // Old NIC: 9 digits + V/X = 2 (year) + 3 (day) + 4 (serial) + suffix
        // New NIC: 4 (year) + 3 (day) + 5 (serial) = 12 digits
        // Rest from old = positions 2-8 = 7 chars, pad serial to 5 digits
        $dayOfYear = substr($nic, 2, 3);
        $serial    = substr($nic, 5, 4);

        return $year . $dayOfYear . str_pad($serial, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get full parsed details from NIC.
     *
     * @return array{valid: bool, format: string|null, birth_year: int|null, gender: string|null}
     */
    public static function parse(string $nic): array
    {
        $nic = strtoupper(trim($nic));

        if (!self::validate($nic)) {
            return ['valid' => false, 'format' => null, 'birth_year' => null, 'gender' => null];
        }

        return [
            'valid'      => true,
            'format'     => self::isOldFormat($nic) ? 'old' : 'new',
            'birth_year' => self::getBirthYear($nic),
            'gender'     => self::getGender($nic),
        ];
    }
}

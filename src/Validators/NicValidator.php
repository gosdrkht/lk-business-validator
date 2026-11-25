<?php

namespace Trishanapp\LkBusinessValidator\Validators;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Sri Lanka National Identity Card (NIC) Validator
 *
 * Supports:
 *  - Old format: 9 digits + V/X  (e.g. 851234567V)
 *  - New format: 12 digits        (e.g. 198512345678)
 *
 * Validates birth year, day-of-year (1–366 male, 501–866 female),
 * leap-year day 60 rule, and extracts DOB, gender, age, and voting eligibility.
 */
class NicValidator
{
    private const MONTH_DAYS = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    private const MIN_YEAR = 1900;

    /**
     * Validate a NIC number (format, birth year, and day-of-year).
     */
    public static function validate(string $nic): bool
    {
        $nic = strtoupper(trim($nic));

        if (!self::isOldFormat($nic) && !self::isNewFormat($nic)) {
            return false;
        }

        $year = self::extractRawYear($nic);
        $rawDay = self::extractRawDay($nic);

        if ($year === null || $rawDay === null) {
            return false;
        }

        $maxYear = (int) date('Y');
        if ($year < self::MIN_YEAR || $year > $maxYear) {
            return false;
        }

        $dayOfYear = $rawDay > 500 ? $rawDay - 500 : $rawDay;
        if ($dayOfYear < 1 || $dayOfYear > 366) {
            return false;
        }

        // Day 60 is Feb 29 in Sri Lanka's 366-day NIC calendar; invalid in non-leap years.
        if ($dayOfYear === 60 && !self::isLeapYear($year)) {
            return false;
        }

        return true;
    }

    /**
     * Check if input matches old format syntax (9 digits + V or X).
     */
    public static function isOldFormat(string $nic): bool
    {
        return (bool) preg_match('/^\d{9}[VX]$/', strtoupper(trim($nic)));
    }

    /**
     * Check if input matches new format syntax (12 digits).
     */
    public static function isNewFormat(string $nic): bool
    {
        return (bool) preg_match('/^\d{12}$/', trim($nic));
    }

    /**
     * Extract birth year from a valid NIC.
     */
    public static function getBirthYear(string $nic): ?int
    {
        $nic = strtoupper(trim($nic));

        if (!self::validate($nic)) {
            return null;
        }

        return self::extractRawYear($nic);
    }

    /**
     * Extract day of year (1–366) from a valid NIC.
     * For females, 500 is subtracted from the encoded day value.
     */
    public static function getDayOfYear(string $nic): ?int
    {
        $nic = strtoupper(trim($nic));

        if (!self::validate($nic)) {
            return null;
        }

        $rawDay = self::extractRawDay($nic);

        return $rawDay > 500 ? $rawDay - 500 : $rawDay;
    }

    /**
     * Extract birth date (YYYY-MM-DD) from a valid NIC.
     *
     * Sri Lanka NIC day-of-year numbering treats every year as having 29 days in February
     * (day 60 = Feb 29, day 61 = Mar 1).
     */
    public static function getBirthDate(string $nic): ?string
    {
        $nic = strtoupper(trim($nic));

        if (!self::validate($nic)) {
            return null;
        }

        $year = self::extractRawYear($nic);
        $day = self::getDayOfYear($nic);

        $month = 1;
        foreach (self::MONTH_DAYS as $daysInMonth) {
            if ($day <= $daysInMonth) {
                break;
            }
            $day -= $daysInMonth;
            $month++;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    /**
     * Calculate age in years from a valid NIC.
     */
    public static function getAge(string $nic, ?DateTimeInterface $asOf = null): ?int
    {
        $birthDate = self::getBirthDate($nic);
        if ($birthDate === null) {
            return null;
        }

        $dob = new DateTimeImmutable($birthDate);
        $now = $asOf ? DateTimeImmutable::createFromInterface($asOf) : new DateTimeImmutable('today');

        if ($dob > $now) {
            return null;
        }

        return $dob->diff($now)->y;
    }

    /**
     * Determine gender ('Male' or 'Female') from a valid NIC.
     */
    public static function getGender(string $nic): ?string
    {
        $nic = strtoupper(trim($nic));

        if (!self::validate($nic)) {
            return null;
        }

        $rawDay = self::extractRawDay($nic);

        return $rawDay > 500 ? 'Female' : 'Male';
    }

    /**
     * Determine voting eligibility from old-format NIC suffix ('V' = true, 'X' = false).
     * Returns null for new 12-digit NICs or invalid NICs.
     */
    public static function canVote(string $nic): ?bool
    {
        $nic = strtoupper(trim($nic));

        if (!self::validate($nic) || !self::isOldFormat($nic)) {
            return null;
        }

        return str_ends_with($nic, 'V');
    }

    /**
     * Convert a valid old NIC format (9 digits + V/X) to the official new 12-digit format.
     *
     * Official DRP formula: YYDDDSSSSV → 19YYDDD0SSSS
     */
    public static function convertToNew(string $nic): ?string
    {
        $nic = strtoupper(trim($nic));

        if (!self::isOldFormat($nic) || !self::validate($nic)) {
            return null;
        }

        $year = '19' . substr($nic, 0, 2);
        $dayOfYear = substr($nic, 2, 3);
        $serial = substr($nic, 5, 4);

        return $year . $dayOfYear . '0' . $serial;
    }

    /**
     * Get full parsed details from NIC.
     *
     * @return array{
     *     valid: bool,
     *     format: string|null,
     *     birth_year: int|null,
     *     birth_date: string|null,
     *     day_of_year: int|null,
     *     gender: string|null,
     *     age: int|null,
     *     can_vote: bool|null
     * }
     */
    public static function parse(string $nic): array
    {
        $nic = strtoupper(trim($nic));

        if (!self::validate($nic)) {
            return [
                'valid'       => false,
                'format'      => null,
                'birth_year'  => null,
                'birth_date'  => null,
                'day_of_year' => null,
                'gender'      => null,
                'age'         => null,
                'can_vote'    => null,
            ];
        }

        return [
            'valid'       => true,
            'format'      => self::isOldFormat($nic) ? 'old' : 'new',
            'birth_year'  => self::getBirthYear($nic),
            'birth_date'  => self::getBirthDate($nic),
            'day_of_year' => self::getDayOfYear($nic),
            'gender'      => self::getGender($nic),
            'age'         => self::getAge($nic),
            'can_vote'    => self::canVote($nic),
        ];
    }

    private static function extractRawYear(string $nic): ?int
    {
        if (self::isOldFormat($nic)) {
            return (int) ('19' . substr($nic, 0, 2));
        }

        if (self::isNewFormat($nic)) {
            return (int) substr($nic, 0, 4);
        }

        return null;
    }

    private static function extractRawDay(string $nic): ?int
    {
        if (self::isOldFormat($nic)) {
            return (int) substr($nic, 2, 3);
        }

        if (self::isNewFormat($nic)) {
            return (int) substr($nic, 4, 3);
        }

        return null;
    }

    private static function isLeapYear(int $year): bool
    {
        return ($year % 4 === 0 && $year % 100 !== 0) || ($year % 400 === 0);
    }
}

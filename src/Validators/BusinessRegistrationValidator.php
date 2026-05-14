<?php

namespace Trishan\LkBusinessValidator\Validators;

/**
 * Sri Lanka Business Registration (BR) Number Validator
 *
 * The Registrar of Companies issues BR numbers in these formats:
 *
 *   PV (Private Limited):         PV12345       — PV + 5 digits
 *   PB (Public Limited):          PB12345       — PB + 5 digits
 *   HP (Partnership):             HP12345       — HP + 5 digits
 *   SP (Sole Proprietor/Trade):   SP123456      — SP + 6 digits (newer)
 *   CS (Consumer Society):        CS12345
 *   GS (Guarantee Company):       GS12345
 *   NA (Non-Profit Association):  NA12345
 *
 * Older formats may also appear as plain 5–7 digit numbers.
 *
 * Note: Sri Lanka does not publish a formal checksum algorithm for BR numbers,
 * so validation is format-based only.
 */
class BusinessRegistrationValidator
{
    private const PREFIXES = ['PV', 'PB', 'HP', 'SP', 'CS', 'GS', 'NA'];

    /**
     * Validate a BR number.
     */
    public static function validate(string $br): bool
    {
        $br = strtoupper(trim($br));

        return self::isStandardFormat($br) || self::isLegacyFormat($br);
    }

    /**
     * Check if the BR is in standard prefix format (e.g., PV12345, SP123456).
     */
    public static function isStandardFormat(string $br): bool
    {
        $br = strtoupper(trim($br));

        // PV/PB/HP/CS/GS/NA: prefix + 5 digits
        if (preg_match('/^(PV|PB|HP|CS|GS|NA)[0-9]{5}$/', $br)) {
            return true;
        }

        // SP: prefix + 5 or 6 digits (newer registrations have more digits)
        if (preg_match('/^SP[0-9]{5,6}$/', $br)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the BR is legacy format (plain 5–7 digit number).
     */
    public static function isLegacyFormat(string $br): bool
    {
        return (bool) preg_match('/^[0-9]{5,7}$/', trim($br));
    }

    /**
     * Extract the company type prefix from a BR number.
     */
    public static function getCompanyType(string $br): ?string
    {
        $br = strtoupper(trim($br));

        $typeMap = [
            'PV' => 'Private Limited Company',
            'PB' => 'Public Limited Company',
            'HP' => 'Partnership',
            'SP' => 'Sole Proprietorship / Trade Name',
            'CS' => 'Consumer Society',
            'GS' => 'Company Limited by Guarantee',
            'NA' => 'Non-profit Association',
        ];

        foreach ($typeMap as $prefix => $label) {
            if (str_starts_with($br, $prefix)) {
                return $label;
            }
        }

        return null;
    }

    /**
     * Normalize a BR number to uppercase with no spaces.
     */
    public static function normalize(string $br): string
    {
        return strtoupper(preg_replace('/\s+/', '', $br));
    }
}

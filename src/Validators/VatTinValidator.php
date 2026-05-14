<?php

namespace Trishan\LkBusinessValidator\Validators;

/**
 * Sri Lanka VAT / TIN (Tax Identification Number) Validator
 *
 * Format:
 *   VAT number: 9 digits followed by 7000 or 7001 suffix (total 13 chars)
 *               e.g., 1234567897000
 *
 *   TIN (general): 9 digits (issued by IRD)
 *                  e.g., 123456789
 *
 * The Inland Revenue Department (IRD) issues TINs.
 * VAT numbers are derived from TINs with a 4-digit suffix.
 */
class VatTinValidator
{
    /**
     * Validate a Sri Lanka VAT number (13-digit with 7000/7001 suffix).
     */
    public static function validateVat(string $vat): bool
    {
        $vat = preg_replace('/[\s\-]/', '', trim($vat));

        // 9-digit TIN + 4-digit suffix (7000 or 7001)
        return (bool) preg_match('/^[0-9]{9}700[01]$/', $vat);
    }

    /**
     * Validate a Sri Lanka TIN (9 digits).
     */
    public static function validateTin(string $tin): bool
    {
        $tin = preg_replace('/[\s\-]/', '', trim($tin));
        return (bool) preg_match('/^[0-9]{9}$/', $tin);
    }

    /**
     * Extract TIN from a VAT number.
     * VAT = TIN + suffix, so first 9 digits are the TIN.
     */
    public static function extractTinFromVat(string $vat): ?string
    {
        $vat = preg_replace('/[\s\-]/', '', trim($vat));

        if (self::validateVat($vat)) {
            return substr($vat, 0, 9);
        }

        return null;
    }

    /**
     * Validate either a VAT or TIN number.
     */
    public static function validate(string $value): bool
    {
        return self::validateVat($value) || self::validateTin($value);
    }

    /**
     * Determine whether a value is a VAT or TIN.
     */
    public static function getType(string $value): ?string
    {
        $value = preg_replace('/[\s\-]/', '', trim($value));

        if (self::validateVat($value)) return 'VAT';
        if (self::validateTin($value)) return 'TIN';

        return null;
    }
}

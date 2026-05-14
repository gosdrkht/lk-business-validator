<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka VAT / TIN Number Validator
 *
 * VAT Registration Number: 7 digits + '-' + 7000 (or similar suffix)
 * TIN (Taxpayer Identification Number): 9 or 10 digits
 *
 * Formats:
 *  - TIN:  123456789 or 1234567890
 *  - VAT:  123456789-7000 or 1234567890
 */
class VatValidator
{
    /**
     * Validate a Sri Lanka TIN number.
     * TIN is 9 digits (old) or 10 digits (new/company).
     */
    public static function validateTin(string $tin): bool
    {
        $tin = preg_replace('/[\s\-]+/', '', trim($tin));
        return (bool) preg_match('/^\d{9,10}$/', $tin);
    }

    /**
     * Validate a Sri Lanka VAT registration number.
     * Format: [9or10 digits]-7000
     */
    public static function validateVat(string $vat): bool
    {
        $vat = strtoupper(trim($vat));

        // With suffix: 123456789-7000
        if (preg_match('/^\d{9,10}-7000$/', $vat)) {
            return true;
        }

        // Without suffix (just the TIN part)
        if (preg_match('/^\d{9,10}$/', $vat)) {
            return true;
        }

        return false;
    }

    /**
     * Normalize VAT to standard format (with -7000 suffix).
     */
    public static function normalize(string $vat): ?string
    {
        $vat = strtoupper(trim($vat));
        $digits = preg_replace('/[^0-9]/', '', $vat);

        if (!preg_match('/^\d{9,10}$/', $digits)) {
            return null;
        }

        return $digits . '-7000';
    }

    /**
     * Parse VAT/TIN.
     *
     * @return array{valid: bool, type: string|null, normalized: string|null}
     */
    public static function parse(string $value): array
    {
        $isVat = self::validateVat($value);
        $isTin = self::validateTin($value);

        if (!$isVat && !$isTin) {
            return ['valid' => false, 'type' => null, 'normalized' => null];
        }

        return [
            'valid'      => true,
            'type'       => str_contains($value, '-7000') ? 'VAT' : 'TIN',
            'normalized' => self::normalize($value),
        ];
    }
}

<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka VAT / TIN Number Validator (Inland Revenue Department)
 *
 * Formats:
 *  - TIN: 9 or 10 digits (e.g. 123456789, 1234567890)
 *  - VAT: TIN + '-7000' or '-7001' (e.g. 123456789-7000, 1234567897000, or plain TIN)
 */
class VatValidator
{
    /**
     * Validate a Sri Lanka TIN number (9 or 10 digits).
     */
    public static function validateTin(string $tin): bool
    {
        $cleaned = preg_replace('/[\s\-]+/', '', trim($tin));

        return (bool) preg_match('/^\d{9,10}$/', (string) $cleaned);
    }

    /**
     * Validate a Sri Lanka VAT registration number.
     *
     * Accepts:
     *  - 9 or 10 digits with -7000 / -7001 suffix (e.g. 123456789-7000)
     *  - 9 or 10 digits with 7000 / 7001 suffix without hyphen (e.g. 1234567897000)
     *  - Plain 9 or 10 digit TIN (backwards-compatible)
     */
    public static function validateVat(string $vat): bool
    {
        $vat = strtoupper(trim($vat));

        if (preg_match('/^\d{9,10}-700[01]$/', $vat)) {
            return true;
        }

        if (preg_match('/^\d{9,10}700[01]$/', $vat)) {
            return true;
        }

        if (preg_match('/^\d{9,10}$/', $vat)) {
            return true;
        }

        return false;
    }

    /**
     * Extract the 9–10 digit TIN from a valid VAT or TIN number.
     */
    public static function extractTin(string $value): ?string
    {
        $cleaned = strtoupper(trim($value));

        if (preg_match('/^(\d{9,10})-700[01]$/', $cleaned, $m)) {
            return $m[1];
        }

        if (preg_match('/^(\d{9,10})700[01]$/', $cleaned, $m)) {
            return $m[1];
        }

        if (self::validateTin($cleaned)) {
            return preg_replace('/[\s\-]+/', '', $cleaned);
        }

        return null;
    }

    /**
     * Normalize VAT/TIN to standard VAT format (<tin>-7000 or <tin>-7001).
     */
    public static function normalize(string $value): ?string
    {
        $cleaned = strtoupper(trim($value));

        if (preg_match('/^(\d{9,10})-(700[01])$/', $cleaned, $m)) {
            return $m[1] . '-' . $m[2];
        }

        if (preg_match('/^(\d{9,10})(700[01])$/', $cleaned, $m)) {
            return $m[1] . '-' . $m[2];
        }

        $tin = self::extractTin($cleaned);
        if ($tin === null) {
            return null;
        }

        return $tin . '-7000';
    }

    /**
     * Parse VAT/TIN into details.
     *
     * @return array{valid: bool, type: string|null, tin: string|null, normalized: string|null}
     */
    public static function parse(string $value): array
    {
        $cleaned = strtoupper(trim($value));
        $isVat = self::validateVat($cleaned);
        $isTin = self::validateTin($cleaned);

        if (!$isVat && !$isTin) {
            return [
                'valid'      => false,
                'type'       => null,
                'tin'        => null,
                'normalized' => null,
            ];
        }

        $hasVatSuffix = (bool) preg_match('/^\d{9,10}-?700[01]$/', $cleaned);

        return [
            'valid'      => true,
            'type'       => $hasVatSuffix ? 'VAT' : 'TIN',
            'tin'        => self::extractTin($cleaned),
            'normalized' => self::normalize($cleaned),
        ];
    }
}

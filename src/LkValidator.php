<?php

namespace Trishanapp\LkBusinessValidator;

use Trishanapp\LkBusinessValidator\Validators\NicValidator;
use Trishanapp\LkBusinessValidator\Validators\PhoneValidator;
use Trishanapp\LkBusinessValidator\Validators\BrValidator;
use Trishanapp\LkBusinessValidator\Validators\PostalCodeValidator;
use Trishanapp\LkBusinessValidator\Validators\VatValidator;

/**
 * Main LkValidator facade.
 *
 * Usage:
 *   LkValidator::nic('851234567V');
 *   LkValidator::phone('+94771234567');
 *   LkValidator::br('PV00123456');
 *   LkValidator::postalCode('00100');
 *   LkValidator::vat('123456789-7000');
 */
class LkValidator
{
    // ─── NIC ───────────────────────────────────────────────────────────────

    public static function nic(string $nic): bool
    {
        return NicValidator::validate($nic);
    }

    public static function nicParse(string $nic): array
    {
        return NicValidator::parse($nic);
    }

    // ─── Phone ─────────────────────────────────────────────────────────────

    public static function phone(string $phone): bool
    {
        return PhoneValidator::validate($phone);
    }

    public static function phoneParse(string $phone): array
    {
        return PhoneValidator::parse($phone);
    }

    public static function phoneNormalize(string $phone): ?string
    {
        return PhoneValidator::normalize($phone);
    }

    public static function phoneToInternational(string $phone): ?string
    {
        return PhoneValidator::toInternational($phone);
    }

    // ─── Business Registration ─────────────────────────────────────────────

    public static function br(string $br): bool
    {
        return BrValidator::validate($br);
    }

    public static function brParse(string $br): array
    {
        return BrValidator::parse($br);
    }

    // ─── Postal Code ───────────────────────────────────────────────────────

    public static function postalCode(string|int $code): bool
    {
        return PostalCodeValidator::validate($code);
    }

    public static function postalCodeParse(string|int $code): array
    {
        return PostalCodeValidator::parse($code);
    }

    // ─── VAT / TIN ─────────────────────────────────────────────────────────

    public static function vat(string $vat): bool
    {
        return VatValidator::validateVat($vat);
    }

    public static function tin(string $tin): bool
    {
        return VatValidator::validateTin($tin);
    }

    public static function vatParse(string $value): array
    {
        return VatValidator::parse($value);
    }

    // ─── Batch Validate ────────────────────────────────────────────────────

    /**
     * Validate multiple fields at once.
     *
     * @param array $data ['nic' => '...', 'phone' => '...', ...]
     * @return array{valid: bool, errors: array}
     */
    public static function validateBatch(array $data): array
    {
        $errors = [];

        if (isset($data['nic']) && !self::nic($data['nic'])) {
            $errors['nic'] = 'Invalid NIC number.';
        }

        if (isset($data['phone']) && !self::phone($data['phone'])) {
            $errors['phone'] = 'Invalid Sri Lanka phone number.';
        }

        if (isset($data['br']) && !self::br($data['br'])) {
            $errors['br'] = 'Invalid Business Registration number.';
        }

        if (isset($data['postal_code']) && !self::postalCode($data['postal_code'])) {
            $errors['postal_code'] = 'Invalid Sri Lanka postal code.';
        }

        if (isset($data['vat']) && !self::vat($data['vat'])) {
            $errors['vat'] = 'Invalid VAT registration number.';
        }

        return [
            'valid'  => empty($errors),
            'errors' => $errors,
        ];
    }
}

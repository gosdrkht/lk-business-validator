<?php

namespace Trishanapp\LkBusinessValidator;

use Trishanapp\LkBusinessValidator\Validators\BankValidator;
use Trishanapp\LkBusinessValidator\Validators\BrValidator;
use Trishanapp\LkBusinessValidator\Validators\EpfValidator;
use Trishanapp\LkBusinessValidator\Validators\NicValidator;
use Trishanapp\LkBusinessValidator\Validators\PassportValidator;
use Trishanapp\LkBusinessValidator\Validators\PhoneValidator;
use Trishanapp\LkBusinessValidator\Validators\PostalCodeValidator;
use Trishanapp\LkBusinessValidator\Validators\VatValidator;
use Trishanapp\LkBusinessValidator\Validators\VehicleValidator;

/**
 * Main LkValidator entry point.
 *
 * Usage:
 *   LkValidator::nic('851234567V');
 *   LkValidator::phone('+94771234567');
 *   LkValidator::br('PV00123456');
 *   LkValidator::postalCode('00100');
 *   LkValidator::vat('123456789-7000');
 *   LkValidator::vehicle('WP CAB-1234');
 *   LkValidator::passport('N1234567');
 *   LkValidator::bankCode('7010');
 *   LkValidator::epf('C/12345');
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

    public static function nicConvertToNew(string $nic): ?string
    {
        return NicValidator::convertToNew($nic);
    }

    // ─── Phone ─────────────────────────────────────────────────────────────

    public static function phone(string $phone): bool
    {
        return PhoneValidator::validate($phone);
    }

    public static function mobile(string $phone): bool
    {
        return PhoneValidator::isMobile($phone);
    }

    public static function landline(string $phone): bool
    {
        return PhoneValidator::isLandline($phone);
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

    public static function phoneFormat(string $phone, string $style = 'local'): ?string
    {
        return PhoneValidator::format($phone, $style);
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

    public static function brNormalize(string $br): ?string
    {
        return BrValidator::normalize($br);
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

    // ─── Vehicle Registration ──────────────────────────────────────────────

    public static function vehicle(string $plate): bool
    {
        return VehicleValidator::validate($plate);
    }

    public static function vehicleParse(string $plate): array
    {
        return VehicleValidator::parse($plate);
    }

    public static function vehicleNormalize(string $plate): ?string
    {
        return VehicleValidator::normalize($plate);
    }

    // ─── Passport ──────────────────────────────────────────────────────────

    public static function passport(string $passport): bool
    {
        return PassportValidator::validate($passport);
    }

    public static function passportParse(string $passport): array
    {
        return PassportValidator::parse($passport);
    }

    // ─── Bank / SWIFT ──────────────────────────────────────────────────────

    public static function bankCode(string|int $code): bool
    {
        return BankValidator::validateBankCode($code);
    }

    public static function bankCodeParse(string|int $code): array
    {
        return BankValidator::parseBankCode($code);
    }

    public static function swift(string $swift): bool
    {
        return BankValidator::validateSwift($swift);
    }

    public static function bankAccount(string $accountNumber): bool
    {
        return BankValidator::validateAccountNumber($accountNumber);
    }

    // ─── EPF / ETF ─────────────────────────────────────────────────────────

    public static function epf(string $value): bool
    {
        return EpfValidator::validate($value);
    }

    public static function epfParse(string $value): array
    {
        return EpfValidator::parse($value);
    }

    // ─── Batch Validate ────────────────────────────────────────────────────

    /**
     * Validate multiple Sri Lanka business fields at once.
     *
     * Supported keys: nic, phone, br, postal_code, vat, tin, vehicle, passport, bank_code, swift, epf
     *
     * @param array<string, string|int> $data
     * @return array{valid: bool, errors: array<string, string>}
     */
    public static function validateBatch(array $data): array
    {
        $errors = [];

        if (isset($data['nic']) && !self::nic((string) $data['nic'])) {
            $errors['nic'] = 'Invalid NIC number.';
        }

        if (isset($data['phone']) && !self::phone((string) $data['phone'])) {
            $errors['phone'] = 'Invalid Sri Lanka phone number.';
        }

        if (isset($data['br']) && !self::br((string) $data['br'])) {
            $errors['br'] = 'Invalid Business Registration number.';
        }

        if (isset($data['postal_code']) && !self::postalCode($data['postal_code'])) {
            $errors['postal_code'] = 'Invalid Sri Lanka postal code.';
        }

        if (isset($data['vat']) && !self::vat((string) $data['vat'])) {
            $errors['vat'] = 'Invalid VAT registration number.';
        }

        if (isset($data['tin']) && !self::tin((string) $data['tin'])) {
            $errors['tin'] = 'Invalid TIN number.';
        }

        if (isset($data['vehicle']) && !self::vehicle((string) $data['vehicle'])) {
            $errors['vehicle'] = 'Invalid Sri Lanka vehicle registration number.';
        }

        if (isset($data['passport']) && !self::passport((string) $data['passport'])) {
            $errors['passport'] = 'Invalid Sri Lanka passport number.';
        }

        if (isset($data['bank_code']) && !self::bankCode($data['bank_code'])) {
            $errors['bank_code'] = 'Invalid Sri Lanka CBSL bank code.';
        }

        if (isset($data['swift']) && !self::swift((string) $data['swift'])) {
            $errors['swift'] = 'Invalid Sri Lanka SWIFT/BIC code.';
        }

        if (isset($data['epf']) && !self::epf((string) $data['epf'])) {
            $errors['epf'] = 'Invalid Sri Lanka EPF number.';
        }

        return [
            'valid'  => empty($errors),
            'errors' => $errors,
        ];
    }
}

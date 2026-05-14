<?php

namespace Trishan\LkBusinessValidator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool validateNic(string $nic)
 * @method static array|null nicDetails(string $nic)
 * @method static string|null nicGender(string $nic)
 * @method static string|null nicDob(string $nic)
 * @method static bool validatePhone(string $phone)
 * @method static bool validateMobile(string $phone)
 * @method static bool validateFixedLine(string $phone)
 * @method static string|null formatPhone(string $phone, string $style = 'local')
 * @method static string|null phoneCarrier(string $phone)
 * @method static bool validateBr(string $br)
 * @method static string|null brCompanyType(string $br)
 * @method static bool validateVat(string $vat)
 * @method static bool validateTin(string $tin)
 * @method static bool validateVatOrTin(string $value)
 * @method static string|null vatTinType(string $value)
 * @method static bool validatePostalCode(string $code)
 * @method static string|null postalDistrict(string $code)
 * @method static array validateAll(array $fields)
 *
 * @see \Trishan\LkBusinessValidator\LkValidator
 */
class LkValidator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'lk-validator';
    }
}

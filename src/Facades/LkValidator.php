<?php

namespace Trishanapp\LkBusinessValidator\Facades;

use Illuminate\Support\Facades\Facade;
use Trishanapp\LkBusinessValidator\LkValidator as LkValidatorService;

/**
 * @method static bool nic(string $nic)
 * @method static array nicParse(string $nic)
 * @method static string|null nicConvertToNew(string $nic)
 * @method static bool phone(string $phone)
 * @method static bool mobile(string $phone)
 * @method static bool landline(string $phone)
 * @method static array phoneParse(string $phone)
 * @method static string|null phoneNormalize(string $phone)
 * @method static string|null phoneToInternational(string $phone)
 * @method static string|null phoneFormat(string $phone, string $style = 'local')
 * @method static bool br(string $br)
 * @method static array brParse(string $br)
 * @method static string|null brNormalize(string $br)
 * @method static bool postalCode(string|int $code)
 * @method static array postalCodeParse(string|int $code)
 * @method static bool vat(string $vat)
 * @method static bool tin(string $tin)
 * @method static array vatParse(string $value)
 * @method static bool vehicle(string $plate)
 * @method static array vehicleParse(string $plate)
 * @method static string|null vehicleNormalize(string $plate)
 * @method static bool passport(string $passport)
 * @method static array passportParse(string $passport)
 * @method static bool bankCode(string|int $code)
 * @method static array bankCodeParse(string|int $code)
 * @method static bool swift(string $swift)
 * @method static bool bankAccount(string $accountNumber)
 * @method static bool epf(string $value)
 * @method static array epfParse(string $value)
 * @method static array validateBatch(array $data)
 *
 * @see \Trishanapp\LkBusinessValidator\LkValidator
 */
class LkValidator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LkValidatorService::class;
    }
}

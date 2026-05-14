<?php

namespace Trishanapp\LkBusinessValidator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;

/**
 * Laravel Service Provider
 *
 * Registers custom validation rules:
 *   lk_nic, lk_phone, lk_br, lk_postal_code, lk_vat, lk_tin
 */
class LkValidatorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /** @var Factory $validator */
        $validator = $this->app['validator'];

        $validator->extend('lk_nic', function ($attribute, $value) {
            return LkValidator::nic((string) $value);
        }, 'The :attribute must be a valid Sri Lanka NIC number.');

        $validator->extend('lk_phone', function ($attribute, $value) {
            return LkValidator::phone((string) $value);
        }, 'The :attribute must be a valid Sri Lanka phone number.');

        $validator->extend('lk_br', function ($attribute, $value) {
            return LkValidator::br((string) $value);
        }, 'The :attribute must be a valid Sri Lanka Business Registration number.');

        $validator->extend('lk_postal_code', function ($attribute, $value) {
            return LkValidator::postalCode((string) $value);
        }, 'The :attribute must be a valid Sri Lanka postal code.');

        $validator->extend('lk_vat', function ($attribute, $value) {
            return LkValidator::vat((string) $value);
        }, 'The :attribute must be a valid Sri Lanka VAT registration number.');

        $validator->extend('lk_tin', function ($attribute, $value) {
            return LkValidator::tin((string) $value);
        }, 'The :attribute must be a valid Sri Lanka TIN number.');
    }

    public function register(): void
    {
        $this->app->singleton(LkValidator::class, function () {
            return new LkValidator();
        });
    }
}

<?php

namespace Trishanapp\LkBusinessValidator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;

/**
 * Laravel Service Provider
 *
 * Registers custom validation rules:
 *   lk_nic, lk_phone, lk_mobile, lk_landline, lk_br, lk_postal_code,
 *   lk_vat, lk_tin, lk_vehicle, lk_passport, lk_bank_code, lk_swift, lk_epf
 *
 * Also loads publishable English, Sinhala (si), and Tamil (ta) translations.
 */
class LkValidatorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $langPath = dirname(__DIR__) . '/lang';

        $this->loadTranslationsFrom($langPath, 'lk-validator');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $langPath => $this->app->langPath('vendor/lk-validator'),
            ], 'lk-validator-lang');
        }

        /** @var Factory $validator */
        $validator = $this->app['validator'];

        $rules = [
            'lk_nic'         => [fn ($v) => LkValidator::nic((string) $v),         'lk_nic',         'The :attribute must be a valid Sri Lanka NIC number.'],
            'lk_phone'       => [fn ($v) => LkValidator::phone((string) $v),       'lk_phone',       'The :attribute must be a valid Sri Lanka phone number.'],
            'lk_mobile'      => [fn ($v) => LkValidator::mobile((string) $v),      'lk_mobile',      'The :attribute must be a valid Sri Lanka mobile number.'],
            'lk_landline'    => [fn ($v) => LkValidator::landline((string) $v),    'lk_landline',    'The :attribute must be a valid Sri Lanka landline number.'],
            'lk_br'          => [fn ($v) => LkValidator::br((string) $v),          'lk_br',          'The :attribute must be a valid Sri Lanka Business Registration number.'],
            'lk_postal_code' => [fn ($v) => LkValidator::postalCode((string) $v),  'lk_postal_code', 'The :attribute must be a valid Sri Lanka postal code.'],
            'lk_vat'         => [fn ($v) => LkValidator::vat((string) $v),         'lk_vat',         'The :attribute must be a valid Sri Lanka VAT registration number.'],
            'lk_tin'         => [fn ($v) => LkValidator::tin((string) $v),         'lk_tin',         'The :attribute must be a valid Sri Lanka TIN number.'],
            'lk_vehicle'     => [fn ($v) => LkValidator::vehicle((string) $v),     'lk_vehicle',     'The :attribute must be a valid Sri Lanka vehicle registration number.'],
            'lk_passport'    => [fn ($v) => LkValidator::passport((string) $v),    'lk_passport',    'The :attribute must be a valid Sri Lanka passport number.'],
            'lk_bank_code'   => [fn ($v) => LkValidator::bankCode((string) $v),    'lk_bank_code',   'The :attribute must be a valid Sri Lanka CBSL bank code.'],
            'lk_swift'       => [fn ($v) => LkValidator::swift((string) $v),       'lk_swift',       'The :attribute must be a valid Sri Lanka SWIFT/BIC code.'],
            'lk_epf'         => [fn ($v) => LkValidator::epf((string) $v),         'lk_epf',         'The :attribute must be a valid Sri Lanka EPF number.'],
        ];

        foreach ($rules as $ruleName => [$callback, $translationKey, $fallback]) {
            $translator = $this->app['translator'] ?? null;
            $key = 'lk-validator::validation.' . $translationKey;
            $message = ($translator && $translator->has($key)) ? $translator->get($key) : $fallback;

            $validator->extend($ruleName, function ($attribute, $value) use ($callback) {
                return $callback($value);
            }, $message);
        }
    }

    public function register(): void
    {
        $this->app->singleton(LkValidator::class, function () {
            return new LkValidator();
        });

        $this->app->alias(LkValidator::class, 'lk-validator');
    }
}

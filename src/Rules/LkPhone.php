<?php

namespace Trishanapp\LkBusinessValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Trishanapp\LkBusinessValidator\Rules\Concerns\TranslatesValidationMessage;
use Trishanapp\LkBusinessValidator\Validators\PhoneValidator;

class LkPhone implements ValidationRule
{
    use TranslatesValidationMessage;

    public function __construct(
        private bool $mobileOnly = false,
        private bool $landlineOnly = false,
    ) {
    }

    public static function mobileOnly(): self
    {
        return new self(mobileOnly: true);
    }

    public static function landlineOnly(): self
    {
        return new self(landlineOnly: true);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $phone = (string) $value;

        if ($this->mobileOnly) {
            if (!PhoneValidator::isMobile($phone)) {
                $fail($this->message('lk_mobile', 'The :attribute must be a valid Sri Lanka mobile number.'));
            }
            return;
        }

        if ($this->landlineOnly) {
            if (!PhoneValidator::isLandline($phone)) {
                $fail($this->message('lk_landline', 'The :attribute must be a valid Sri Lanka landline number.'));
            }
            return;
        }

        if (!PhoneValidator::validate($phone)) {
            $fail($this->message('lk_phone', 'The :attribute must be a valid Sri Lanka phone number.'));
        }
    }
}

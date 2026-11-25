<?php

namespace Trishanapp\LkBusinessValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Trishanapp\LkBusinessValidator\Rules\Concerns\TranslatesValidationMessage;
use Trishanapp\LkBusinessValidator\Validators\PostalCodeValidator;

class LkPostalCode implements ValidationRule
{
    use TranslatesValidationMessage;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!PostalCodeValidator::validate(is_int($value) ? $value : (string) $value)) {
            $fail($this->message('lk_postal_code', 'The :attribute must be a valid Sri Lanka postal code.'));
        }
    }
}

<?php

namespace Trishanapp\LkBusinessValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Trishanapp\LkBusinessValidator\Rules\Concerns\TranslatesValidationMessage;
use Trishanapp\LkBusinessValidator\Validators\VatValidator;

class LkVat implements ValidationRule
{
    use TranslatesValidationMessage;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!VatValidator::validateVat((string) $value)) {
            $fail($this->message('lk_vat', 'The :attribute must be a valid Sri Lanka VAT registration number.'));
        }
    }
}

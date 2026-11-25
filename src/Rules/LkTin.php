<?php

namespace Trishanapp\LkBusinessValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Trishanapp\LkBusinessValidator\Rules\Concerns\TranslatesValidationMessage;
use Trishanapp\LkBusinessValidator\Validators\VatValidator;

class LkTin implements ValidationRule
{
    use TranslatesValidationMessage;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!VatValidator::validateTin((string) $value)) {
            $fail($this->message('lk_tin', 'The :attribute must be a valid Sri Lanka TIN number.'));
        }
    }
}

<?php

namespace Trishanapp\LkBusinessValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Trishanapp\LkBusinessValidator\Rules\Concerns\TranslatesValidationMessage;
use Trishanapp\LkBusinessValidator\Validators\BankValidator;

class LkBankCode implements ValidationRule
{
    use TranslatesValidationMessage;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!BankValidator::validateBankCode(is_int($value) ? $value : (string) $value)) {
            $fail($this->message('lk_bank_code', 'The :attribute must be a valid Sri Lanka CBSL bank code.'));
        }
    }
}

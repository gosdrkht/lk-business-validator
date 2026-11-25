<?php

namespace Trishanapp\LkBusinessValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Trishanapp\LkBusinessValidator\Rules\Concerns\TranslatesValidationMessage;
use Trishanapp\LkBusinessValidator\Validators\PassportValidator;

class LkPassport implements ValidationRule
{
    use TranslatesValidationMessage;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!PassportValidator::validate((string) $value)) {
            $fail($this->message('lk_passport', 'The :attribute must be a valid Sri Lanka passport number.'));
        }
    }
}

<?php

namespace Trishanapp\LkBusinessValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Trishanapp\LkBusinessValidator\Rules\Concerns\TranslatesValidationMessage;
use Trishanapp\LkBusinessValidator\Validators\BrValidator;

class LkBr implements ValidationRule
{
    use TranslatesValidationMessage;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!BrValidator::validate((string) $value)) {
            $fail($this->message('lk_br', 'The :attribute must be a valid Sri Lanka Business Registration number.'));
        }
    }
}

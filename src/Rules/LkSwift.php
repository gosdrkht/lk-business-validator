<?php

namespace Trishanapp\LkBusinessValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Trishanapp\LkBusinessValidator\Rules\Concerns\TranslatesValidationMessage;
use Trishanapp\LkBusinessValidator\Validators\BankValidator;

class LkSwift implements ValidationRule
{
    use TranslatesValidationMessage;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!BankValidator::validateSwift((string) $value)) {
            $fail($this->message('lk_swift', 'The :attribute must be a valid Sri Lanka SWIFT/BIC code.'));
        }
    }
}

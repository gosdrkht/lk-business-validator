<?php

namespace Trishanapp\LkBusinessValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Trishanapp\LkBusinessValidator\Rules\Concerns\TranslatesValidationMessage;
use Trishanapp\LkBusinessValidator\Validators\EpfValidator;

class LkEpf implements ValidationRule
{
    use TranslatesValidationMessage;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!EpfValidator::validate((string) $value)) {
            $fail($this->message('lk_epf', 'The :attribute must be a valid Sri Lanka EPF number.'));
        }
    }
}

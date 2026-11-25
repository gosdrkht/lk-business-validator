<?php

namespace Trishanapp\LkBusinessValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Trishanapp\LkBusinessValidator\Rules\Concerns\TranslatesValidationMessage;
use Trishanapp\LkBusinessValidator\Validators\VehicleValidator;

class LkVehicle implements ValidationRule
{
    use TranslatesValidationMessage;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!VehicleValidator::validate((string) $value)) {
            $fail($this->message('lk_vehicle', 'The :attribute must be a valid Sri Lanka vehicle registration number.'));
        }
    }
}

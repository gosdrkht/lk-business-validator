<?php

namespace Trishanapp\LkBusinessValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Trishanapp\LkBusinessValidator\Rules\Concerns\TranslatesValidationMessage;
use Trishanapp\LkBusinessValidator\Validators\NicValidator;

class LkNic implements ValidationRule
{
    use TranslatesValidationMessage;

    public function __construct(
        private ?int $minAge = null,
        private ?string $format = null,
    ) {
    }

    public static function minAge(int $age): self
    {
        return new self(minAge: $age);
    }

    public static function newFormatOnly(): self
    {
        return new self(format: 'new');
    }

    public static function oldFormatOnly(): self
    {
        return new self(format: 'old');
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $nic = (string) $value;

        if (!NicValidator::validate($nic)) {
            $fail($this->message('lk_nic', 'The :attribute must be a valid Sri Lanka NIC number.'));
            return;
        }

        if ($this->format === 'new' && !NicValidator::isNewFormat($nic)) {
            $fail($this->message('lk_nic_format', 'The :attribute must be in the :format Sri Lanka NIC format.', ['format' => 'new']));
            return;
        }

        if ($this->format === 'old' && !NicValidator::isOldFormat($nic)) {
            $fail($this->message('lk_nic_format', 'The :attribute must be in the :format Sri Lanka NIC format.', ['format' => 'old']));
            return;
        }

        if ($this->minAge !== null) {
            $age = NicValidator::getAge($nic);
            if ($age === null || $age < $this->minAge) {
                $fail($this->message('lk_nic_min_age', 'The :attribute holder must be at least :age years old.', ['age' => $this->minAge]));
            }
        }
    }
}

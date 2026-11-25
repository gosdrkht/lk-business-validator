<?php

namespace Illuminate\Contracts\Validation {
    if (!interface_exists(ValidationRule::class)) {
        interface ValidationRule
        {
            public function validate(string $attribute, mixed $value, \Closure $fail): void;
        }
    }
}

namespace Trishanapp\LkBusinessValidator\Tests {

    use PHPUnit\Framework\TestCase;
    use Trishanapp\LkBusinessValidator\Rules\LkBankCode;
    use Trishanapp\LkBusinessValidator\Rules\LkBr;
    use Trishanapp\LkBusinessValidator\Rules\LkEpf;
    use Trishanapp\LkBusinessValidator\Rules\LkNic;
    use Trishanapp\LkBusinessValidator\Rules\LkPassport;
    use Trishanapp\LkBusinessValidator\Rules\LkPhone;
    use Trishanapp\LkBusinessValidator\Rules\LkPostalCode;
    use Trishanapp\LkBusinessValidator\Rules\LkSwift;
    use Trishanapp\LkBusinessValidator\Rules\LkTin;
    use Trishanapp\LkBusinessValidator\Rules\LkVat;
    use Trishanapp\LkBusinessValidator\Rules\LkVehicle;

    class RulesTest extends TestCase
    {
        private function runRule(object $rule, mixed $value): ?string
        {
            $error = null;
            $rule->validate('field', $value, function (string $message) use (&$error): void {
                $error = $message;
            });

            return $error;
        }

        public function test_lk_nic_rule_options(): void
        {
            $this->assertNull($this->runRule(new LkNic(), '851234567V'));
            $this->assertNotNull($this->runRule(new LkNic(), '859994567V'));

            $this->assertNull($this->runRule(LkNic::newFormatOnly(), '198512345678'));
            $this->assertNotNull($this->runRule(LkNic::newFormatOnly(), '851234567V'));

            $this->assertNull($this->runRule(LkNic::oldFormatOnly(), '851234567V'));
            $this->assertNotNull($this->runRule(LkNic::oldFormatOnly(), '198512345678'));

            $this->assertNull($this->runRule(LkNic::minAge(18), '199012345678'));
            $recentYear = (int) date('Y') - 5;
            $this->assertNotNull($this->runRule(LkNic::minAge(18), $recentYear . '10012345'));
        }

        public function test_lk_phone_rule_options(): void
        {
            $this->assertNull($this->runRule(new LkPhone(), '0771234567'));
            $this->assertNull($this->runRule(new LkPhone(), '0112345678'));
            $this->assertNotNull($this->runRule(new LkPhone(), '0991234567'));

            $this->assertNull($this->runRule(LkPhone::mobileOnly(), '0771234567'));
            $this->assertNotNull($this->runRule(LkPhone::mobileOnly(), '0112345678'));

            $this->assertNull($this->runRule(LkPhone::landlineOnly(), '0112345678'));
            $this->assertNotNull($this->runRule(LkPhone::landlineOnly(), '0771234567'));
        }

        public function test_all_other_rules(): void
        {
            $this->assertNull($this->runRule(new LkBr(), 'PV00123456'));
            $this->assertNotNull($this->runRule(new LkBr(), 'XX123'));

            $this->assertNull($this->runRule(new LkPostalCode(), '00100'));
            $this->assertNotNull($this->runRule(new LkPostalCode(), '99999'));

            $this->assertNull($this->runRule(new LkVat(), '123456789-7000'));
            $this->assertNotNull($this->runRule(new LkVat(), '123'));

            $this->assertNull($this->runRule(new LkTin(), '123456789'));
            $this->assertNotNull($this->runRule(new LkTin(), '123'));

            $this->assertNull($this->runRule(new LkVehicle(), 'WP CAB-1234'));
            $this->assertNotNull($this->runRule(new LkVehicle(), 'INVALID'));

            $this->assertNull($this->runRule(new LkPassport(), 'N1234567'));
            $this->assertNotNull($this->runRule(new LkPassport(), 'Z1234567'));

            $this->assertNull($this->runRule(new LkBankCode(), '7010'));
            $this->assertNotNull($this->runRule(new LkBankCode(), '9999'));

            $this->assertNull($this->runRule(new LkSwift(), 'BCEYLKLX'));
            $this->assertNotNull($this->runRule(new LkSwift(), 'BCEYUSLX'));

            $this->assertNull($this->runRule(new LkEpf(), 'C/12345'));
            $this->assertNotNull($this->runRule(new LkEpf(), '12345'));
        }

        public function test_translation_files_have_identical_keys(): void
        {
            $en = require dirname(__DIR__) . '/lang/en/validation.php';
            $si = require dirname(__DIR__) . '/lang/si/validation.php';
            $ta = require dirname(__DIR__) . '/lang/ta/validation.php';

            $this->assertSame(array_keys($en), array_keys($si));
            $this->assertSame(array_keys($en), array_keys($ta));
        }
    }
}

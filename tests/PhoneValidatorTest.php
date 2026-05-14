<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishanapp\LkBusinessValidator\Validators\PhoneValidator;

class PhoneValidatorTest extends TestCase
{
    public function test_valid_local_mobile(): void
    {
        $this->assertTrue(PhoneValidator::validate('0771234567'));
    }

    public function test_valid_international_format(): void
    {
        $this->assertTrue(PhoneValidator::validate('+94771234567'));
    }

    public function test_valid_without_plus(): void
    {
        $this->assertTrue(PhoneValidator::validate('94771234567'));
    }

    public function test_valid_with_spaces(): void
    {
        $this->assertTrue(PhoneValidator::validate('077 123 4567'));
    }

    public function test_valid_landline_colombo(): void
    {
        $this->assertTrue(PhoneValidator::validate('0112345678'));
    }

    public function test_invalid_too_short(): void
    {
        $this->assertFalse(PhoneValidator::validate('077123'));
    }

    public function test_invalid_wrong_prefix(): void
    {
        $this->assertFalse(PhoneValidator::validate('0991234567'));
    }

    public function test_normalize_international_to_local(): void
    {
        $this->assertEquals('0771234567', PhoneValidator::normalize('+94771234567'));
    }

    public function test_to_international(): void
    {
        $this->assertEquals('+94771234567', PhoneValidator::toInternational('0771234567'));
    }

    public function test_detect_dialog_network(): void
    {
        $this->assertEquals('Dialog', PhoneValidator::getNetwork('0771234567'));
    }

    public function test_is_mobile(): void
    {
        $this->assertTrue(PhoneValidator::isMobile('0771234567'));
        $this->assertFalse(PhoneValidator::isMobile('0112345678'));
    }
}

<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishanapp\LkBusinessValidator\Validators\PhoneValidator;

class PhoneValidatorTest extends TestCase
{
    public function test_valid_local_mobile(): void
    {
        $this->assertTrue(PhoneValidator::validate('0771234567'));
        $this->assertTrue(PhoneValidator::validate('0701234567'));
        $this->assertTrue(PhoneValidator::validate('0711234567'));
        $this->assertTrue(PhoneValidator::validate('0721234567'));
        $this->assertTrue(PhoneValidator::validate('0741234567'));
        $this->assertTrue(PhoneValidator::validate('0751234567'));
        $this->assertTrue(PhoneValidator::validate('0761234567'));
        $this->assertTrue(PhoneValidator::validate('0781234567'));
    }

    public function test_valid_international_formats(): void
    {
        $this->assertTrue(PhoneValidator::validate('+94771234567'));
        $this->assertTrue(PhoneValidator::validate('0094771234567'));
        $this->assertTrue(PhoneValidator::validate('94771234567'));
        $this->assertTrue(PhoneValidator::validate('077 123 4567'));
        $this->assertTrue(PhoneValidator::validate('+94 77-123-4567'));
    }

    public function test_valid_landlines(): void
    {
        $this->assertTrue(PhoneValidator::validate('0112345678')); // Colombo
        $this->assertTrue(PhoneValidator::validate('0812345678')); // Kandy
        $this->assertTrue(PhoneValidator::validate('0912345678')); // Galle
    }

    public function test_invalid_numbers(): void
    {
        $this->assertFalse(PhoneValidator::validate('077123'));
        $this->assertFalse(PhoneValidator::validate('0991234567'));
        $this->assertFalse(PhoneValidator::validate('0731234567'));
        $this->assertFalse(PhoneValidator::validate('0791234567'));
        $this->assertFalse(PhoneValidator::validate('0000000000'));
        $this->assertFalse(PhoneValidator::validate(''));
    }

    public function test_normalize_and_format(): void
    {
        $this->assertSame('0771234567', PhoneValidator::normalize('+94771234567'));
        $this->assertSame('0771234567', PhoneValidator::normalize('0094771234567'));
        $this->assertSame('+94771234567', PhoneValidator::toInternational('0771234567'));
        $this->assertSame('077 123 4567', PhoneValidator::format('0771234567', 'local'));
        $this->assertSame('+94 77 123 4567', PhoneValidator::format('0771234567', 'international'));
        $this->assertSame('+94771234567', PhoneValidator::format('0771234567', 'e164'));
    }

    public function test_network_and_area_detection(): void
    {
        $this->assertSame('Dialog', PhoneValidator::getNetwork('0771234567'));
        $this->assertSame('Dialog', PhoneValidator::getNetwork('0741234567'));
        $this->assertSame('Mobitel', PhoneValidator::getNetwork('0701234567'));
        $this->assertSame('Mobitel', PhoneValidator::getNetwork('0711234567'));
        $this->assertSame('Hutch', PhoneValidator::getNetwork('0721234567'));
        $this->assertSame('Hutch', PhoneValidator::getNetwork('0781234567'));
        $this->assertSame('Airtel', PhoneValidator::getNetwork('0751234567'));
        $this->assertSame('SLT', PhoneValidator::getNetwork('0812345678'));
        $this->assertSame('Kandy', PhoneValidator::getArea('0812345678'));
        $this->assertSame('Galle', PhoneValidator::getArea('0912345678'));
        $this->assertNull(PhoneValidator::getArea('0771234567'));
    }

    public function test_is_mobile_and_is_landline(): void
    {
        $this->assertTrue(PhoneValidator::isMobile('0771234567'));
        $this->assertFalse(PhoneValidator::isMobile('0112345678'));
        $this->assertTrue(PhoneValidator::isLandline('0112345678'));
        $this->assertFalse(PhoneValidator::isLandline('0771234567'));
    }

    public function test_parse_returns_full_details(): void
    {
        $result = PhoneValidator::parse('+94812345678');
        $this->assertTrue($result['valid']);
        $this->assertSame('0812345678', $result['normalized']);
        $this->assertSame('+94812345678', $result['international']);
        $this->assertSame('081 234 5678', $result['formatted']);
        $this->assertSame('SLT', $result['network']);
        $this->assertSame('landline', $result['type']);
        $this->assertSame('Kandy', $result['area']);
    }
}

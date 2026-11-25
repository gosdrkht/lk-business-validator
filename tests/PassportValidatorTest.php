<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishanapp\LkBusinessValidator\Validators\PassportValidator;

class PassportValidatorTest extends TestCase
{
    public function test_valid_passports(): void
    {
        $this->assertTrue(PassportValidator::validate('N1234567'));
        $this->assertTrue(PassportValidator::validate('n 1234567'));
        $this->assertTrue(PassportValidator::validate('M7654321'));
        $this->assertTrue(PassportValidator::validate('OL1234567'));
        $this->assertTrue(PassportValidator::validate('D1234567'));
        $this->assertTrue(PassportValidator::validate('E1234567'));
    }

    public function test_invalid_passports(): void
    {
        $this->assertFalse(PassportValidator::validate('Z1234567'));
        $this->assertFalse(PassportValidator::validate('N123456'));
        $this->assertFalse(PassportValidator::validate('N12345678'));
        $this->assertFalse(PassportValidator::validate(''));
    }

    public function test_parse_passport(): void
    {
        $parsed = PassportValidator::parse('ol-1234567');
        $this->assertTrue($parsed['valid']);
        $this->assertSame('OL1234567', $parsed['normalized']);
        $this->assertSame('OL', $parsed['series']);
        $this->assertSame('Official Passport', $parsed['type']);
        $this->assertSame('1234567', $parsed['number']);
    }
}

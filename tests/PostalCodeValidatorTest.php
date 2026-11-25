<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishanapp\LkBusinessValidator\Validators\PostalCodeValidator;

class PostalCodeValidatorTest extends TestCase
{
    public function test_valid_postal_codes(): void
    {
        $this->assertTrue(PostalCodeValidator::validate('00100')); // Colombo 1
        $this->assertTrue(PostalCodeValidator::validate(100));     // Colombo 1 as int
        $this->assertTrue(PostalCodeValidator::validate('10100')); // Colombo suburbs
        $this->assertTrue(PostalCodeValidator::validate('20000')); // Kandy
        $this->assertTrue(PostalCodeValidator::validate('80000')); // Galle
        $this->assertTrue(PostalCodeValidator::validate('40000')); // Jaffna
        $this->assertTrue(PostalCodeValidator::validate('90000')); // Badulla
    }

    public function test_invalid_postal_codes(): void
    {
        $this->assertFalse(PostalCodeValidator::validate('999'));
        $this->assertFalse(PostalCodeValidator::validate('99999'));
        $this->assertFalse(PostalCodeValidator::validate('15000'));
        $this->assertFalse(PostalCodeValidator::validate('00000'));
        $this->assertFalse(PostalCodeValidator::validate('ABCDE'));
        $this->assertFalse(PostalCodeValidator::validate(''));
    }

    public function test_province_and_district_lookup(): void
    {
        $this->assertSame('Western', PostalCodeValidator::getProvince('00100'));
        $this->assertSame('Colombo', PostalCodeValidator::getDistrict('00100'));
        $this->assertSame('Central', PostalCodeValidator::getProvince('20000'));
        $this->assertSame('Kandy', PostalCodeValidator::getDistrict('20000'));
        $this->assertSame('Southern', PostalCodeValidator::getProvince('80000'));
        $this->assertSame('Galle', PostalCodeValidator::getDistrict('80000'));
        $this->assertNull(PostalCodeValidator::getProvince('99999'));
        $this->assertNull(PostalCodeValidator::getDistrict('99999'));
    }

    public function test_parse(): void
    {
        $result = PostalCodeValidator::parse(100);
        $this->assertTrue($result['valid']);
        $this->assertSame('00100', $result['code']);
        $this->assertSame('Western', $result['province']);
        $this->assertSame('Colombo', $result['district']);
    }
}

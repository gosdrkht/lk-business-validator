<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishanapp\LkBusinessValidator\Validators\VehicleValidator;

class VehicleValidatorTest extends TestCase
{
    public function test_valid_vehicle_numbers(): void
    {
        $this->assertTrue(VehicleValidator::validate('WP CAB-1234'));
        $this->assertTrue(VehicleValidator::validate('cp ka 5678'));
        $this->assertTrue(VehicleValidator::validate('CAB-1234'));
        $this->assertTrue(VehicleValidator::validate('KA1234'));
        $this->assertTrue(VehicleValidator::validate('250-1234'));
        $this->assertTrue(VehicleValidator::validate('19-1234'));
        $this->assertTrue(VehicleValidator::validate('12 SRI 1234'));
        $this->assertTrue(VehicleValidator::validate('1 ශ්‍රී 4567'));
    }

    public function test_invalid_vehicle_numbers(): void
    {
        $this->assertFalse(VehicleValidator::validate('ZZ CAB-1234'));
        $this->assertFalse(VehicleValidator::validate('WP CAB-0000'));
        $this->assertFalse(VehicleValidator::validate('ABCD-1234'));
        $this->assertFalse(VehicleValidator::validate('1234'));
        $this->assertFalse(VehicleValidator::validate(''));
    }

    public function test_normalize_and_parse(): void
    {
        $this->assertSame('WP CAB-1234', VehicleValidator::normalize('wp cab 1234'));
        $this->assertSame('Western', VehicleValidator::getProvince('WP CAB-1234'));
        $this->assertSame('modern', VehicleValidator::getFormat('CAB-1234'));
        $this->assertSame('numeric', VehicleValidator::getFormat('250-1234'));
        $this->assertSame('vintage', VehicleValidator::getFormat('12 ශ්‍රී 1234'));

        $parsed = VehicleValidator::parse('sp cae 5678');
        $this->assertTrue($parsed['valid']);
        $this->assertSame('SP CAE-5678', $parsed['normalized']);
        $this->assertSame('SP', $parsed['province_code']);
        $this->assertSame('Southern', $parsed['province']);
        $this->assertSame('CAE', $parsed['prefix']);
        $this->assertSame('5678', $parsed['number']);
    }
}

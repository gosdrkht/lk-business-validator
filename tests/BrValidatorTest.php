<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishanapp\LkBusinessValidator\Validators\BrValidator;

class BrValidatorTest extends TestCase
{
    public function test_valid_formats(): void
    {
        $this->assertTrue(BrValidator::validate('PV00123456'));
        $this->assertTrue(BrValidator::validate('PV/00123456'));
        $this->assertTrue(BrValidator::validate('PV-00123456'));
        $this->assertTrue(BrValidator::validate('PV 00123456'));
        $this->assertTrue(BrValidator::validate('PB12345'));
        $this->assertTrue(BrValidator::validate('GA12345'));
        $this->assertTrue(BrValidator::validate('FC123456'));
        $this->assertTrue(BrValidator::validate('cs12345'));
    }

    public function test_invalid_formats(): void
    {
        $this->assertFalse(BrValidator::validate('XX12345678'));
        $this->assertFalse(BrValidator::validate('PV1234'));
        $this->assertFalse(BrValidator::validate('PV123456789'));
        $this->assertFalse(BrValidator::validate(''));
    }

    public function test_normalize(): void
    {
        $this->assertSame('PV00123456', BrValidator::normalize('pv/00123456'));
        $this->assertSame('PV00123456', BrValidator::normalize('PV-00123456'));
        $this->assertSame('PV00123456', BrValidator::normalize('PV 00123456'));
        $this->assertNull(BrValidator::normalize('XX12345'));
    }

    public function test_get_company_type(): void
    {
        $this->assertSame('Private Limited Company', BrValidator::getCompanyType('PV00123456'));
        $this->assertSame('Partnership', BrValidator::getCompanyType('PR00123456'));
        $this->assertSame('Guarantee Company', BrValidator::getCompanyType('GA12345'));
        $this->assertSame('Overseas Company', BrValidator::getCompanyType('FC12345'));
    }

    public function test_parse_returns_details(): void
    {
        $result = BrValidator::parse('PV/00123456');
        $this->assertTrue($result['valid']);
        $this->assertSame('PV00123456', $result['normalized']);
        $this->assertSame('PV', $result['prefix']);
        $this->assertSame('Private Limited Company', $result['company_type']);
    }
}

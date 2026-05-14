<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishanapp\LkBusinessValidator\Validators\BrValidator;

class BrValidatorTest extends TestCase
{
    public function test_valid_pv_format(): void
    {
        $this->assertTrue(BrValidator::validate('PV00123456'));
    }

    public function test_valid_with_slash(): void
    {
        $this->assertTrue(BrValidator::validate('PV/00123456'));
    }

    public function test_valid_pb_format(): void
    {
        $this->assertTrue(BrValidator::validate('PB12345'));
    }

    public function test_invalid_unknown_prefix(): void
    {
        $this->assertFalse(BrValidator::validate('XX12345678'));
    }

    public function test_invalid_too_short_digits(): void
    {
        $this->assertFalse(BrValidator::validate('PV1234'));
    }

    public function test_get_company_type_private(): void
    {
        $this->assertEquals('Private Limited Company', BrValidator::getCompanyType('PV00123456'));
    }

    public function test_get_company_type_partnership(): void
    {
        $this->assertEquals('Partnership', BrValidator::getCompanyType('PR00123456'));
    }

    public function test_parse_returns_details(): void
    {
        $result = BrValidator::parse('PV00123456');
        $this->assertTrue($result['valid']);
        $this->assertEquals('PV', $result['prefix']);
        $this->assertEquals('Private Limited Company', $result['company_type']);
    }
}

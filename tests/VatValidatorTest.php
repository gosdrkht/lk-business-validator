<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishanapp\LkBusinessValidator\Validators\VatValidator;

class VatValidatorTest extends TestCase
{
    public function test_valid_vat_and_tin(): void
    {
        $this->assertTrue(VatValidator::validateVat('123456789-7000'));
        $this->assertTrue(VatValidator::validateVat('123456789-7001'));
        $this->assertTrue(VatValidator::validateVat('1234567897000'));
        $this->assertTrue(VatValidator::validateVat('1234567890-7000'));
        $this->assertTrue(VatValidator::validateVat('123456789'));
        $this->assertTrue(VatValidator::validateTin('123456789'));
        $this->assertTrue(VatValidator::validateTin('1234567890'));
    }

    public function test_invalid_vat_and_tin(): void
    {
        $this->assertFalse(VatValidator::validateVat('123456789-7002'));
        $this->assertFalse(VatValidator::validateVat('12345'));
        $this->assertFalse(VatValidator::validateVat(''));
        $this->assertFalse(VatValidator::validateTin('12345678'));
        $this->assertFalse(VatValidator::validateTin('12345678901'));
    }

    public function test_normalize_and_extract_tin(): void
    {
        $this->assertSame('123456789-7000', VatValidator::normalize('123456789-7000'));
        $this->assertSame('123456789-7001', VatValidator::normalize('123456789-7001'));
        $this->assertSame('123456789-7000', VatValidator::normalize('1234567897000'));
        $this->assertSame('123456789-7000', VatValidator::normalize('123456789'));
        $this->assertNull(VatValidator::normalize('invalid'));

        $this->assertSame('123456789', VatValidator::extractTin('123456789-7000'));
        $this->assertSame('123456789', VatValidator::extractTin('1234567897001'));
        $this->assertSame('1234567890', VatValidator::extractTin('1234567890'));
    }

    public function test_parse(): void
    {
        $vat = VatValidator::parse('123456789-7000');
        $this->assertTrue($vat['valid']);
        $this->assertSame('VAT', $vat['type']);
        $this->assertSame('123456789', $vat['tin']);
        $this->assertSame('123456789-7000', $vat['normalized']);

        $tin = VatValidator::parse('123456789');
        $this->assertTrue($tin['valid']);
        $this->assertSame('TIN', $tin['type']);
        $this->assertSame('123456789', $tin['tin']);
        $this->assertSame('123456789-7000', $tin['normalized']);
    }
}

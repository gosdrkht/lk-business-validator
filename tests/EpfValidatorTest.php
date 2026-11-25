<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishanapp\LkBusinessValidator\Validators\EpfValidator;

class EpfValidatorTest extends TestCase
{
    public function test_valid_epf_numbers(): void
    {
        $this->assertTrue(EpfValidator::validateEmployerNumber('C/12345'));
        $this->assertTrue(EpfValidator::validateEmployerNumber('A/123456'));
        $this->assertTrue(EpfValidator::validateEmployerNumber('c12345'));
        $this->assertTrue(EpfValidator::validateMemberNumber('001234'));
        $this->assertTrue(EpfValidator::validateMemberNumber(42));
        $this->assertTrue(EpfValidator::validate('C/12345'));
        $this->assertTrue(EpfValidator::validate('C/12345/001234'));
    }

    public function test_invalid_epf_numbers(): void
    {
        $this->assertFalse(EpfValidator::validateEmployerNumber('12345'));
        $this->assertFalse(EpfValidator::validateEmployerNumber('C/123'));
        $this->assertFalse(EpfValidator::validateMemberNumber('0'));
        $this->assertFalse(EpfValidator::validateMemberNumber('000000'));
        $this->assertFalse(EpfValidator::validate('C/12345/0000'));
    }

    public function test_normalize_and_parse(): void
    {
        $this->assertSame('C/12345', EpfValidator::normalizeEmployerNumber('c 12345'));

        $parsed = EpfValidator::parse('c/12345/001234');
        $this->assertTrue($parsed['valid']);
        $this->assertSame('C/12345/001234', $parsed['normalized']);
        $this->assertSame('C', $parsed['zone']);
        $this->assertSame('C/12345', $parsed['employer_number']);
        $this->assertSame('001234', $parsed['member_number']);
    }
}

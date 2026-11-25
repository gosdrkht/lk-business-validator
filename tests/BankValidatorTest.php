<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishanapp\LkBusinessValidator\Validators\BankValidator;

class BankValidatorTest extends TestCase
{
    public function test_valid_cbsl_bank_codes(): void
    {
        $this->assertTrue(BankValidator::validateBankCode('7010'));
        $this->assertTrue(BankValidator::validateBankCode(7056));
        $this->assertTrue(BankValidator::validateBankCode('7083'));
        $this->assertTrue(BankValidator::validateBankCode('7278'));
        $this->assertSame('Bank of Ceylon', BankValidator::getBankName('7010'));
        $this->assertSame('Commercial Bank of Ceylon PLC', BankValidator::getBankName('7056'));
    }

    public function test_invalid_bank_codes(): void
    {
        $this->assertFalse(BankValidator::validateBankCode('9999'));
        $this->assertFalse(BankValidator::validateBankCode('701'));
        $this->assertNull(BankValidator::getBankName('9999'));
    }

    public function test_swift_validation(): void
    {
        $this->assertTrue(BankValidator::validateSwift('BCEYLKLX'));
        $this->assertTrue(BankValidator::validateSwift('cceylklx001'));
        $this->assertTrue(BankValidator::validateSwift('HBLILKLX'));
        $this->assertFalse(BankValidator::validateSwift('BCEYUSLX')); // not LK
        $this->assertFalse(BankValidator::validateSwift('INVALID'));
    }

    public function test_branch_and_account_numbers(): void
    {
        $this->assertTrue(BankValidator::validateBranchCode('001'));
        $this->assertFalse(BankValidator::validateBranchCode('12'));
        $this->assertTrue(BankValidator::validateAccountNumber('0012-3456-7890'));
        $this->assertTrue(BankValidator::validateAccountNumber('12345678'));
        $this->assertFalse(BankValidator::validateAccountNumber('1234567'));
    }

    public function test_parse_bank_code(): void
    {
        $parsed = BankValidator::parseBankCode('7278');
        $this->assertTrue($parsed['valid']);
        $this->assertSame('7278', $parsed['code']);
        $this->assertSame('Sampath Bank PLC', $parsed['bank_name']);
    }
}

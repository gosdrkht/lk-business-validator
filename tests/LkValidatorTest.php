<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishanapp\LkBusinessValidator\LkValidator;

class LkValidatorTest extends TestCase
{
    public function test_all_facade_entry_points(): void
    {
        $this->assertTrue(LkValidator::nic('851234567V'));
        $this->assertSame('198512304567', LkValidator::nicConvertToNew('851234567V'));
        $this->assertTrue(LkValidator::phone('+94771234567'));
        $this->assertTrue(LkValidator::mobile('0771234567'));
        $this->assertTrue(LkValidator::landline('0112345678'));
        $this->assertSame('077 123 4567', LkValidator::phoneFormat('0771234567', 'local'));
        $this->assertTrue(LkValidator::br('PV/00123456'));
        $this->assertSame('PV00123456', LkValidator::brNormalize('PV/00123456'));
        $this->assertTrue(LkValidator::postalCode('00100'));
        $this->assertTrue(LkValidator::vat('123456789-7000'));
        $this->assertTrue(LkValidator::tin('123456789'));
        $this->assertTrue(LkValidator::vehicle('WP CAB-1234'));
        $this->assertTrue(LkValidator::passport('N1234567'));
        $this->assertTrue(LkValidator::bankCode('7010'));
        $this->assertTrue(LkValidator::swift('BCEYLKLX'));
        $this->assertTrue(LkValidator::bankAccount('001234567890'));
        $this->assertTrue(LkValidator::epf('C/12345'));
    }

    public function test_validate_batch_all_valid(): void
    {
        $result = LkValidator::validateBatch([
            'nic'         => '851234567V',
            'phone'       => '0771234567',
            'br'          => 'PV00123456',
            'postal_code' => '00100',
            'vat'         => '123456789-7000',
            'tin'         => '123456789',
            'vehicle'     => 'WP CAB-1234',
            'passport'    => 'N1234567',
            'bank_code'   => '7056',
            'swift'       => 'CCEYLKLX',
            'epf'         => 'C/12345',
        ]);

        $this->assertTrue($result['valid']);
        $this->assertSame([], $result['errors']);
    }

    public function test_validate_batch_collects_errors(): void
    {
        $result = LkValidator::validateBatch([
            'nic'         => '859994567V',
            'phone'       => '0991234567',
            'br'          => 'XX123',
            'postal_code' => '99999',
            'vat'         => 'invalid',
            'tin'         => '123',
            'vehicle'     => 'INVALID',
            'passport'    => 'Z1234567',
            'bank_code'   => '9999',
            'swift'       => 'BCEYUSLX',
            'epf'         => '123',
        ]);

        $this->assertFalse($result['valid']);
        $this->assertCount(11, $result['errors']);
        $this->assertArrayHasKey('tin', $result['errors']);
        $this->assertArrayHasKey('vehicle', $result['errors']);
        $this->assertArrayHasKey('epf', $result['errors']);
    }
}

<?php

namespace Trishan\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishan\LkBusinessValidator\LkValidator;
use Trishan\LkBusinessValidator\Validators\NicValidator;
use Trishan\LkBusinessValidator\Validators\PhoneValidator;
use Trishan\LkBusinessValidator\Validators\BusinessRegistrationValidator;
use Trishan\LkBusinessValidator\Validators\VatTinValidator;
use Trishan\LkBusinessValidator\Validators\PostalCodeValidator;

class LkValidatorTest extends TestCase
{
    // ─── NIC Tests ────────────────────────────────────────────────────────────

    public function test_valid_old_format_nic(): void
    {
        $this->assertTrue(NicValidator::validate('891234567V'));
        $this->assertTrue(NicValidator::validate('891234567v')); // lowercase
        $this->assertTrue(NicValidator::validate('901234567X'));
    }

    public function test_valid_new_format_nic(): void
    {
        $this->assertTrue(NicValidator::validate('198912345678'));
        $this->assertTrue(NicValidator::validate('200012345678'));
    }

    public function test_invalid_nic(): void
    {
        $this->assertFalse(NicValidator::validate('12345'));
        $this->assertFalse(NicValidator::validate('ABCDEFGHIV'));
        $this->assertFalse(NicValidator::validate('89123456A'));
        $this->assertFalse(NicValidator::validate(''));
    }

    public function test_nic_gender_extraction(): void
    {
        // Male NIC (day_of_year < 500)
        $this->assertSame('male', NicValidator::getGender('891234567V'));

        // Female NIC (day_of_year > 500, meaning 500+day encoded)
        // Day 012 = male; Day 512 = female (day 12)
        $maleDayNic  = '89' . '012' . '12345' . 'V'; // 9 + V = 10 chars
        $femaleDayNic = '89' . '512' . '12345' . 'V';
        $this->assertSame('male',   NicValidator::getGender('890121234V'));
        $this->assertSame('female', NicValidator::getGender('895121234V'));
    }

    public function test_nic_dob_extraction(): void
    {
        // 89 + 100 (day 100) = April 10 (non-leap year 1989)
        $details = NicValidator::extractDetails('891001234V');
        $this->assertSame(1989, $details['year']);
        $this->assertSame(100, $details['day_of_year']);
        $this->assertSame('male', $details['gender']);
    }

    // ─── Phone Tests ──────────────────────────────────────────────────────────

    public function test_valid_phone_numbers(): void
    {
        $this->assertTrue(PhoneValidator::validate('0771234567'));
        $this->assertTrue(PhoneValidator::validate('+94771234567'));
        $this->assertTrue(PhoneValidator::validate('0094771234567'));
        $this->assertTrue(PhoneValidator::validate('94771234567'));
    }

    public function test_invalid_phone_numbers(): void
    {
        $this->assertFalse(PhoneValidator::validate('077123456'));    // too short
        $this->assertFalse(PhoneValidator::validate('07712345678'));  // too long
        $this->assertFalse(PhoneValidator::validate('abcdefghij'));
        $this->assertFalse(PhoneValidator::validate(''));
    }

    public function test_mobile_validation(): void
    {
        $this->assertTrue(PhoneValidator::validateMobile('0771234567'));
        $this->assertTrue(PhoneValidator::validateMobile('0781234567')); // Hutch
        $this->assertFalse(PhoneValidator::validateMobile('0112345678')); // fixed line
    }

    public function test_fixed_line_validation(): void
    {
        $this->assertTrue(PhoneValidator::validateFixedLine('0112345678')); // Colombo
        $this->assertFalse(PhoneValidator::validateFixedLine('0771234567')); // mobile
    }

    public function test_phone_carrier_detection(): void
    {
        $this->assertSame('Dialog', PhoneValidator::getCarrier('0771234567'));
        $this->assertSame('Hutch',  PhoneValidator::getCarrier('0781234567'));
        $this->assertSame('Mobitel', PhoneValidator::getCarrier('0711234567'));
    }

    public function test_phone_format(): void
    {
        $this->assertSame('077 123 4567',   PhoneValidator::format('0771234567', 'local'));
        $this->assertSame('+94771234567',   PhoneValidator::format('0771234567', 'e164'));
        $this->assertSame('+94 77 123 4567', PhoneValidator::format('0771234567', 'international'));
    }

    // ─── BR Tests ─────────────────────────────────────────────────────────────

    public function test_valid_br_numbers(): void
    {
        $this->assertTrue(BusinessRegistrationValidator::validate('PV12345'));
        $this->assertTrue(BusinessRegistrationValidator::validate('PB12345'));
        $this->assertTrue(BusinessRegistrationValidator::validate('HP12345'));
        $this->assertTrue(BusinessRegistrationValidator::validate('SP123456'));
        $this->assertTrue(BusinessRegistrationValidator::validate('1234567')); // legacy
    }

    public function test_invalid_br_numbers(): void
    {
        $this->assertFalse(BusinessRegistrationValidator::validate('XX12345'));
        $this->assertFalse(BusinessRegistrationValidator::validate('PV123'));   // too short
        $this->assertFalse(BusinessRegistrationValidator::validate(''));
    }

    public function test_br_company_type(): void
    {
        $this->assertSame('Private Limited Company', BusinessRegistrationValidator::getCompanyType('PV12345'));
        $this->assertSame('Partnership',             BusinessRegistrationValidator::getCompanyType('HP12345'));
        $this->assertNull(BusinessRegistrationValidator::getCompanyType('1234567')); // legacy has no prefix
    }

    // ─── VAT / TIN Tests ──────────────────────────────────────────────────────

    public function test_valid_vat(): void
    {
        $this->assertTrue(VatTinValidator::validateVat('1234567897000'));
        $this->assertTrue(VatTinValidator::validateVat('1234567897001'));
    }

    public function test_invalid_vat(): void
    {
        $this->assertFalse(VatTinValidator::validateVat('123456789'));     // too short
        $this->assertFalse(VatTinValidator::validateVat('1234567897002')); // wrong suffix
    }

    public function test_valid_tin(): void
    {
        $this->assertTrue(VatTinValidator::validateTin('123456789'));
    }

    public function test_extract_tin_from_vat(): void
    {
        $this->assertSame('123456789', VatTinValidator::extractTinFromVat('1234567897000'));
    }

    // ─── Postal Code Tests ────────────────────────────────────────────────────

    public function test_valid_postal_codes(): void
    {
        $this->assertTrue(PostalCodeValidator::validate('10001')); // Colombo
        $this->assertTrue(PostalCodeValidator::validate('20000')); // Kandy
    }

    public function test_invalid_postal_codes(): void
    {
        $this->assertFalse(PostalCodeValidator::validate('1000'));   // too short
        $this->assertFalse(PostalCodeValidator::validate('100001')); // too long
        $this->assertFalse(PostalCodeValidator::validate('ABCDE'));
    }

    public function test_postal_district(): void
    {
        $this->assertSame('Colombo',      PostalCodeValidator::getDistrict('10001'));
        $this->assertSame('Kandy',        PostalCodeValidator::getDistrict('20000'));
        $this->assertSame('Galle',        PostalCodeValidator::getDistrict('30000'));
        $this->assertNull(PostalCodeValidator::getDistrict('99999')); // unknown prefix
    }

    // ─── Combined validateAll Tests ───────────────────────────────────────────

    public function test_validate_all(): void
    {
        $results = LkValidator::validateAll([
            'nic'         => '891234567V',
            'phone'       => '0771234567',
            'br'          => 'PV12345',
            'postal_code' => '10001',
        ]);

        $this->assertTrue($results['nic']);
        $this->assertTrue($results['phone']);
        $this->assertTrue($results['br']);
        $this->assertTrue($results['postal_code']);
    }
}

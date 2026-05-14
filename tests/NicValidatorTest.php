<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use PHPUnit\Framework\TestCase;
use Trishanapp\LkBusinessValidator\Validators\NicValidator;

class NicValidatorTest extends TestCase
{
    // ─── Valid cases ──────────────────────────────────────────────────────

    public function test_valid_old_format_with_v(): void
    {
        $this->assertTrue(NicValidator::validate('851234567V'));
    }

    public function test_valid_old_format_with_x(): void
    {
        $this->assertTrue(NicValidator::validate('851234567X'));
    }

    public function test_valid_old_format_lowercase_v(): void
    {
        $this->assertTrue(NicValidator::validate('851234567v'));
    }

    public function test_valid_new_format(): void
    {
        $this->assertTrue(NicValidator::validate('198512345678'));
    }

    // ─── Invalid cases ────────────────────────────────────────────────────

    public function test_invalid_too_short(): void
    {
        $this->assertFalse(NicValidator::validate('8512345V'));
    }

    public function test_invalid_wrong_suffix(): void
    {
        $this->assertFalse(NicValidator::validate('851234567A'));
    }

    public function test_invalid_too_many_digits_new(): void
    {
        $this->assertFalse(NicValidator::validate('1985123456789'));
    }

    public function test_invalid_empty(): void
    {
        $this->assertFalse(NicValidator::validate(''));
    }

    // ─── Parsing ─────────────────────────────────────────────────────────

    public function test_parse_old_format_birth_year(): void
    {
        $result = NicValidator::parse('851234567V');
        $this->assertEquals(1985, $result['birth_year']);
    }

    public function test_parse_new_format_birth_year(): void
    {
        $result = NicValidator::parse('199012345678');
        $this->assertEquals(1990, $result['birth_year']);
    }

    public function test_parse_gender_male(): void
    {
        // Day 123 < 500 = Male
        $result = NicValidator::parse('851234567V');
        $this->assertEquals('Male', $result['gender']);
    }

    public function test_parse_gender_female(): void
    {
        // Day 623 > 500 = Female
        $result = NicValidator::parse('856234567V');
        $this->assertEquals('Female', $result['gender']);
    }

    public function test_convert_old_to_new(): void
    {
        $new = NicValidator::convertToNew('851234567V');
        $this->assertEquals('198512345678', $new);
    }
}

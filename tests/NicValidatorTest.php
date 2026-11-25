<?php

namespace Trishanapp\LkBusinessValidator\Tests;

use DateTimeImmutable;
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
        $this->assertTrue(NicValidator::validate('200056012345')); // leap year female Feb 29 (day 60)
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

    public function test_invalid_day_of_year(): void
    {
        $this->assertFalse(NicValidator::validate('850004567V'));   // day 000
        $this->assertFalse(NicValidator::validate('854004567V'));   // day 400 (invalid)
        $this->assertFalse(NicValidator::validate('855004567V'));   // day 500 (invalid)
        $this->assertFalse(NicValidator::validate('859994567V'));   // day 999
        $this->assertFalse(NicValidator::validate('198506012345')); // Feb 29 in non-leap year 1985
    }

    public function test_invalid_year_range(): void
    {
        $this->assertFalse(NicValidator::validate('189912345678'));
        $this->assertFalse(NicValidator::validate('299912345678'));
    }

    // ─── Parsing & helpers ────────────────────────────────────────────────

    public function test_parse_old_format_details(): void
    {
        $result = NicValidator::parse('851234567V');
        $this->assertTrue($result['valid']);
        $this->assertSame('old', $result['format']);
        $this->assertSame(1985, $result['birth_year']);
        $this->assertSame('1985-05-02', $result['birth_date']);
        $this->assertSame(123, $result['day_of_year']);
        $this->assertSame('Male', $result['gender']);
        $this->assertTrue($result['can_vote']);
    }

    public function test_parse_new_format_birth_year(): void
    {
        $result = NicValidator::parse('199012345678');
        $this->assertTrue($result['valid']);
        $this->assertSame('new', $result['format']);
        $this->assertSame(1990, $result['birth_year']);
        $this->assertNull($result['can_vote']);
    }

    public function test_parse_gender_female(): void
    {
        $result = NicValidator::parse('856234567V');
        $this->assertSame('Female', $result['gender']);
        $this->assertSame(123, $result['day_of_year']);
    }

    public function test_can_vote_suffixes(): void
    {
        $this->assertTrue(NicValidator::canVote('851234567V'));
        $this->assertFalse(NicValidator::canVote('851234567X'));
        $this->assertNull(NicValidator::canVote('198512345678'));
    }

    public function test_get_birth_date_march_1_and_leap_day(): void
    {
        // Day 61 is always March 1 in Sri Lanka NIC numbering
        $this->assertSame('1985-03-01', NicValidator::getBirthDate('850611234V'));
        // Day 60 in leap year 2000 is Feb 29
        $this->assertSame('2000-02-29', NicValidator::getBirthDate('200006012345'));
        // Day 1 is Jan 1
        $this->assertSame('1995-01-01', NicValidator::getBirthDate('950011234V'));
        // Day 366 is Dec 31
        $this->assertSame('1995-12-31', NicValidator::getBirthDate('953661234V'));
    }

    public function test_get_age(): void
    {
        $asOf = new DateTimeImmutable('2025-06-01');
        $this->assertSame(40, NicValidator::getAge('851234567V', $asOf));
    }

    public function test_convert_old_to_new(): void
    {
        // Official DRP conversion: YYDDDSSSSV -> 19YYDDD0SSSS
        $this->assertSame('198512304567', NicValidator::convertToNew('851234567V'));
        $this->assertNull(NicValidator::convertToNew('198512345678'));
        $this->assertNull(NicValidator::convertToNew('859994567V'));
    }
}

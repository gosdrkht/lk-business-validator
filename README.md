# 🇱🇰 lk-business-validator

[![Packagist](https://img.shields.io/packagist/v/trishanapp/lk-business-validator)](https://packagist.org/packages/trishanapp/lk-business-validator)
[![Tests](https://github.com/gosdrkht/lk-business-validator/actions/workflows/tests.yml/badge.svg)](https://github.com/gosdrkht/lk-business-validator/actions/workflows/tests.yml)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue)](https://php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**The PHP & Laravel validation library built specifically for Sri Lanka.**

Validate and parse Sri Lanka NIC numbers, Business Registration numbers, phone numbers, postal codes, VAT/TIN numbers, vehicle registration plates, passports, CBSL bank & SWIFT codes, and EPF/ETF numbers — with modern Laravel rules and English, Sinhala (`si`), and Tamil (`ta`) translations out of the box.

---

## 📦 Installation

```bash
composer require trishanapp/lk-business-validator
```

Laravel users: the service provider and `LkValidator` facade auto-discover — no manual config needed.

---

## 🚀 Quick Start

```php
use Trishanapp\LkBusinessValidator\LkValidator;

// NIC (old & new formats, with day-of-year & leap-year validation)
LkValidator::nic('851234567V');         // true
LkValidator::nic('198512345678');       // true

// Phone (mobile & TRC landline area codes)
LkValidator::phone('0771234567');       // true
LkValidator::phone('+94771234567');     // true
LkValidator::mobile('0711234567');      // true
LkValidator::landline('0812345678');    // true (Kandy)

// Business Registration
LkValidator::br('PV00123456');          // true
LkValidator::br('PV/00123456');         // true

// Postal Code
LkValidator::postalCode('00100');       // true (Colombo)
LkValidator::postalCode('80000');       // true (Galle)

// VAT / TIN
LkValidator::vat('123456789-7000');     // true
LkValidator::tin('123456789');          // true

// Vehicle Registration
LkValidator::vehicle('WP CAB-1234');    // true
LkValidator::vehicle('250-1234');       // true

// Passport
LkValidator::passport('N1234567');      // true

// Bank / SWIFT
LkValidator::bankCode('7010');          // true (Bank of Ceylon)
LkValidator::swift('BCEYLKLX');         // true

// EPF / ETF
LkValidator::epf('C/12345');            // true
LkValidator::epf('C/12345/001234');     // true
```

---

## 📖 Full API Reference

### NIC Validator

```php
use Trishanapp\LkBusinessValidator\LkValidator;
use Trishanapp\LkBusinessValidator\Validators\NicValidator;

LkValidator::nic('851234567V');         // true
LkValidator::nic('859994567V');         // false (invalid day of year)

LkValidator::nicParse('851234567V');
// [
//   'valid'       => true,
//   'format'      => 'old',
//   'birth_year'  => 1985,
//   'birth_date'  => '1985-05-02',
//   'day_of_year' => 123,
//   'gender'      => 'Male',
//   'age'         => ...,
//   'can_vote'    => true,
// ]

NicValidator::getBirthDate('850611234V');  // '1985-03-01'
NicValidator::getGender('856234567V');     // 'Female'
NicValidator::canVote('851234567X');       // false
NicValidator::convertToNew('851234567V');  // '198512304567'
```

---

### Phone Validator

Supports local (`0`), `+94`, `0094`, and `94` formats with strict TRC mobile prefixes (`070`, `071`, `072`, `074`, `075`, `076`, `077`, `078`) and all Sri Lanka landline area codes (`011`, `081`, `091`, etc.).

```php
LkValidator::phoneParse('+94812345678');
// [
//   'valid'         => true,
//   'normalized'    => '0812345678',
//   'international' => '+94812345678',
//   'formatted'     => '081 234 5678',
//   'network'       => 'SLT',
//   'type'          => 'landline',
//   'area'          => 'Kandy',
// ]

LkValidator::phoneNormalize('+94771234567');              // '0771234567'
LkValidator::phoneToInternational('0771234567');          // '+94771234567'
LkValidator::phoneFormat('0771234567', 'international');  // '+94 77 123 4567'
```

Operators detected: **Dialog** (`074`, `076`, `077`), **Mobitel** (`070`, `071`), **Hutch** (`072`, `078`), **Airtel** (`075`), **SLT** (all TRC landline area codes).

---

### Business Registration Validator

Supported prefixes: `PV`, `PB`, `HP`, `FC`, `SP`, `PR`, `GN`, `GA`, `GL`, `SC`, `CS`, `NA`. Accepts `/`, `-`, or space separators.

```php
LkValidator::brParse('PV/00123456');
// [
//   'valid'        => true,
//   'normalized'   => 'PV00123456',
//   'prefix'       => 'PV',
//   'company_type' => 'Private Limited Company',
// ]
```

---

### Postal Code Validator

Validates against real Sri Lanka Post district and province ranges.

```php
LkValidator::postalCodeParse('80000');
// [
//   'valid'    => true,
//   'code'     => '80000',
//   'province' => 'Southern',
//   'district' => 'Galle',
// ]
```

---

### VAT / TIN Validator

```php
LkValidator::vatParse('123456789-7000');
// [
//   'valid'      => true,
//   'type'       => 'VAT',
//   'tin'        => '123456789',
//   'normalized' => '123456789-7000',
// ]
```

---

### Vehicle Registration Validator

Supports modern provincial (`WP CAB-1234`), non-provincial (`CAB-1234`), old numeric (`250-1234`), and vintage Sri (`12 SRI 1234`, `1 ශ්‍රී 4567`) formats.

```php
LkValidator::vehicleParse('wp cab 1234');
// [
//   'valid'         => true,
//   'normalized'    => 'WP CAB-1234',
//   'format'        => 'modern',
//   'province_code' => 'WP',
//   'province'      => 'Western',
//   'prefix'        => 'CAB',
//   'number'        => '1234',
// ]
```

---

### Passport Validator

Supports Ordinary (`N`, `M`), Official (`OL`), Diplomatic (`D`), and Emergency Certificate (`E`) series.

```php
LkValidator::passportParse('OL-1234567');
// [
//   'valid'      => true,
//   'normalized' => 'OL1234567',
//   'series'     => 'OL',
//   'type'       => 'Official Passport',
//   'number'     => '1234567',
// ]
```

---

### Bank / CBSL & SWIFT Validator

```php
LkValidator::bankCodeParse('7056');
// [
//   'valid'     => true,
//   'code'      => '7056',
//   'bank_name' => 'Commercial Bank of Ceylon PLC',
// ]

LkValidator::swift('BCEYLKLX');          // true
LkValidator::bankAccount('0012-3456-7890'); // true
```

---

### EPF / ETF Validator

```php
LkValidator::epfParse('C/12345/001234');
// [
//   'valid'           => true,
//   'normalized'      => 'C/12345/001234',
//   'zone'            => 'C',
//   'employer_number' => 'C/12345',
//   'member_number'   => '001234',
// ]
```

---

### Batch Validation

```php
$result = LkValidator::validateBatch([
    'nic'         => '851234567V',
    'phone'       => '0771234567',
    'br'          => 'PV00123456',
    'postal_code' => '00100',
    'vat'         => '123456789-7000',
    'tin'         => '123456789',
    'vehicle'     => 'WP CAB-1234',
    'passport'    => 'N1234567',
    'bank_code'   => '7010',
    'swift'       => 'BCEYLKLX',
    'epf'         => 'C/12345',
]);

// ['valid' => true, 'errors' => []]
```

---

## 🔧 Laravel Integration

### String Rules

```php
$request->validate([
    'nic'         => ['required', 'lk_nic'],
    'phone'       => ['required', 'lk_phone'],
    'mobile'      => ['required', 'lk_mobile'],
    'landline'    => ['nullable', 'lk_landline'],
    'br_number'   => ['required', 'lk_br'],
    'postal_code' => ['required', 'lk_postal_code'],
    'vat_number'  => ['nullable', 'lk_vat'],
    'tin_number'  => ['nullable', 'lk_tin'],
    'vehicle'     => ['nullable', 'lk_vehicle'],
    'passport'    => ['nullable', 'lk_passport'],
    'bank_code'   => ['nullable', 'lk_bank_code'],
    'swift'       => ['nullable', 'lk_swift'],
    'epf_number'  => ['nullable', 'lk_epf'],
]);
```

### Modern Invokable Rule Classes (Laravel 10 / 11 / 12)

```php
use Trishanapp\LkBusinessValidator\Rules\LkNic;
use Trishanapp\LkBusinessValidator\Rules\LkPhone;
use Trishanapp\LkBusinessValidator\Rules\LkVehicle;
use Trishanapp\LkBusinessValidator\Rules\LkBankCode;

$request->validate([
    'nic'       => ['required', LkNic::minAge(18)],
    'new_nic'   => ['nullable', LkNic::newFormatOnly()],
    'mobile'    => ['required', LkPhone::mobileOnly()],
    'office'    => ['nullable', LkPhone::landlineOnly()],
    'vehicle'   => ['nullable', new LkVehicle()],
    'bank_code' => ['nullable', new LkBankCode()],
]);
```

### Multilingual Translations (English, Sinhala, Tamil)

Validation messages are bundled in `en`, `si` (Sinhala), and `ta` (Tamil). To customize them in your Laravel app:

```bash
php artisan vendor:publish --tag=lk-validator-lang
```

---

## 🧪 Running Tests

```bash
composer install
composer test
```

---

## 🏗️ Built for Sri Lankan Developers

This package is maintained by [Trishan](https://leadingedgesolutions.lk) — founder of [Leading Edge Solutions](https://leadingedgesolutions.lk) and builder of Sri Lankan SaaS products including [Ceylon Ledger](https://ceylonledger.lk).

If this package saved you time, consider [sponsoring on GitHub](https://github.com/sponsors/trishanapp) ☕

---

## 📄 License

MIT © [Trishan / Leading Edge Solutions](https://leadingedgesolutions.lk)

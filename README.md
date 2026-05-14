# 🇱🇰 lk-business-validator

[![Packagist](https://img.shields.io/packagist/v/trishanapp/lk-business-validator)](https://packagist.org/packages/trishanapp/lk-business-validator)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue)](https://php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-passing-brightgreen)](#)

**The only PHP validation library built specifically for Sri Lanka.**

Validate NIC numbers, Business Registration numbers, phone numbers, postal codes, and VAT/TIN numbers — with parsing, normalization, and Laravel rule integration out of the box.

---

## 📦 Installation

```bash
composer require trishanapp/lk-business-validator
```

Laravel users: the service provider auto-discovers — no config needed.

---

## 🚀 Quick Start

```php
use Trishanapp\LkBusinessValidator\LkValidator;

// NIC
LkValidator::nic('851234567V');       // true
LkValidator::nic('198512345678');     // true (new format)

// Phone
LkValidator::phone('0771234567');     // true
LkValidator::phone('+94771234567');   // true

// Business Registration
LkValidator::br('PV00123456');        // true
LkValidator::br('PV/00123456');       // true (slash accepted)

// Postal Code
LkValidator::postalCode('00100');     // true (Colombo)
LkValidator::postalCode('80000');     // true (Galle)

// VAT / TIN
LkValidator::vat('123456789-7000');   // true
LkValidator::tin('123456789');        // true
```

---

## 📖 Full API Reference

### NIC Validator

```php
use Trishanapp\LkBusinessValidator\LkValidator;

// Validate (old format: 9 digits + V/X, new format: 12 digits)
LkValidator::nic('851234567V');       // true
LkValidator::nic('198512345678');     // true

// Parse — returns full details
LkValidator::nicParse('851234567V');
// [
//   'valid'      => true,
//   'format'     => 'old',
//   'birth_year' => 1985,
//   'gender'     => 'Male',
// ]

// Direct helpers
NicValidator::getBirthYear('851234567V');       // 1985
NicValidator::getGender('856234567V');          // 'Female' (day > 500)
NicValidator::convertToNew('851234567V');       // '198512304567'
```

---

### Phone Validator

```php
// Validate — supports local, +94, and 94 prefixes
LkValidator::phone('0771234567');      // true
LkValidator::phone('+94771234567');    // true
LkValidator::phone('077 123 4567');    // true (spaces OK)

// Parse
LkValidator::phoneParse('0771234567');
// [
//   'valid'         => true,
//   'normalized'    => '0771234567',
//   'international' => '+94771234567',
//   'network'       => 'Dialog',
//   'type'          => 'mobile',
// ]

// Helpers
LkValidator::phoneNormalize('+94771234567');     // '0771234567'
LkValidator::phoneToInternational('0771234567'); // '+94771234567'
```

Networks detected: **Dialog**, **Dialog/Mobitel**, **Airtel**, **Hutch**, **SLT**

---

### Business Registration Validator

```php
// Supported prefixes: PV, PB, HP, SP, PR, GN, SC
LkValidator::br('PV00123456');    // true — Private Limited
LkValidator::br('PR00123456');    // true — Partnership
LkValidator::br('PV/00123456');   // true — slash format OK

// Parse
LkValidator::brParse('PV00123456');
// [
//   'valid'        => true,
//   'normalized'   => 'PV00123456',
//   'prefix'       => 'PV',
//   'company_type' => 'Private Limited Company',
// ]
```

| Prefix | Type |
|--------|------|
| PV     | Private Limited Company |
| PB     | Public Company |
| HP     | Foreign Company (Branch) |
| SP     | Sole Proprietorship |
| PR     | Partnership |
| GN     | Guarantee Company |
| SC     | Unlimited Company |

---

### Postal Code Validator

```php
LkValidator::postalCode('00100');  // true — Colombo 1
LkValidator::postalCode('80000');  // true — Galle
LkValidator::postalCode('999');    // false — too short

// Parse with province detection
LkValidator::postalCodeParse('80000');
// [
//   'valid'    => true,
//   'code'     => '80000',
//   'province' => 'Southern',
// ]
```

---

### VAT / TIN Validator

```php
LkValidator::vat('123456789-7000');  // true
LkValidator::vat('123456789');       // true (without suffix)
LkValidator::tin('123456789');       // true (9 digits)
LkValidator::tin('1234567890');      // true (10 digits)

// Parse
LkValidator::vatParse('123456789');
// [
//   'valid'      => true,
//   'type'       => 'TIN',
//   'normalized' => '123456789-7000',
// ]
```

---

### Batch Validation

```php
$result = LkValidator::validateBatch([
    'nic'          => '851234567V',
    'phone'        => '0771234567',
    'br'           => 'PV00123456',
    'postal_code'  => '00100',
    'vat'          => '123456789-7000',
]);

// ['valid' => true, 'errors' => []]
```

---

## 🔧 Laravel Integration

After installing, these validation rules are automatically available:

```php
$request->validate([
    'nic'          => ['required', 'lk_nic'],
    'phone'        => ['required', 'lk_phone'],
    'br_number'    => ['required', 'lk_br'],
    'postal_code'  => ['required', 'lk_postal_code'],
    'vat_number'   => ['nullable', 'lk_vat'],
    'tin_number'   => ['nullable', 'lk_tin'],
]);
```

Custom error messages:

```php
// lang/en/validation.php
'lk_nic'         => 'Please enter a valid Sri Lanka NIC number.',
'lk_phone'       => 'Please enter a valid Sri Lanka phone number.',
'lk_br'          => 'Please enter a valid Business Registration number.',
'lk_postal_code' => 'Please enter a valid Sri Lanka postal code.',
'lk_vat'         => 'Please enter a valid VAT registration number.',
```

---

## 🧪 Running Tests

```bash
composer install
./vendor/bin/phpunit
```

---

## 🏗️ Built With Love for Sri Lankan Developers

This package is maintained by [Trishan](https://leadingedgesolutions.lk) — founder of [Leading Edge Solutions](https://leadingedgesolutions.lk) and builder of Sri Lankan SaaS products including [Ceylon Ledger](https://ceylonledger.lk).

If this package saved you time, consider [sponsoring on GitHub](https://github.com/sponsors/trishanapp) ☕

---

## 📄 License

MIT © [Trishan / Leading Edge Solutions](https://leadingedgesolutions.lk)

<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka Phone Number Validator
 *
 * Supports local (07x, 011, 081, 091, etc.) and international (+94, 0094, 94) formats
 * with strict TRC mobile prefix and landline area code validation.
 */
class PhoneValidator
{
    /** @var array<string, string> Mobile 3-digit prefix => operator name */
    public const MOBILE_PREFIXES = [
        '070' => 'Mobitel',
        '071' => 'Mobitel',
        '072' => 'Hutch',
        '074' => 'Dialog',
        '075' => 'Airtel',
        '076' => 'Dialog',
        '077' => 'Dialog',
        '078' => 'Hutch',
    ];

    /** @var array<string, string> TRC landline area code => city/district */
    public const LANDLINE_AREAS = [
        '011' => 'Colombo',
        '021' => 'Jaffna',
        '023' => 'Mannar',
        '024' => 'Vavuniya',
        '025' => 'Anuradhapura',
        '026' => 'Trincomalee',
        '027' => 'Polonnaruwa',
        '031' => 'Negombo',
        '032' => 'Chilaw',
        '033' => 'Gampaha',
        '034' => 'Kalutara',
        '035' => 'Kegalle',
        '036' => 'Avissawella',
        '037' => 'Kurunegala',
        '038' => 'Panadura',
        '041' => 'Matara',
        '045' => 'Ratnapura',
        '047' => 'Hambantota',
        '051' => 'Hatton',
        '052' => 'Nuwara Eliya',
        '054' => 'Nawalapitiya',
        '055' => 'Badulla',
        '057' => 'Bandarawela',
        '063' => 'Ampara',
        '065' => 'Batticaloa',
        '066' => 'Matale',
        '067' => 'Kalmunai',
        '081' => 'Kandy',
        '091' => 'Galle',
    ];

    /**
     * Validate a Sri Lanka phone number (mobile or landline).
     */
    public static function validate(string $phone): bool
    {
        return self::normalize($phone) !== null;
    }

    /**
     * Normalize a valid Sri Lanka phone number to local 10-digit format (0xxxxxxxxx).
     */
    public static function normalize(string $phone): ?string
    {
        $cleaned = preg_replace('/[\s\-\(\)\.]+/', '', trim($phone));
        if ($cleaned === null || $cleaned === '') {
            return null;
        }

        // 0094xxxxxxxxx → 0xxxxxxxxx
        if (preg_match('/^0094(\d{9})$/', $cleaned, $m)) {
            $cleaned = '0' . $m[1];
        } elseif (preg_match('/^\+94(\d{9})$/', $cleaned, $m)) {
            // +94xxxxxxxxx → 0xxxxxxxxx
            $cleaned = '0' . $m[1];
        } elseif (preg_match('/^94(\d{9})$/', $cleaned, $m)) {
            // 94xxxxxxxxx → 0xxxxxxxxx
            $cleaned = '0' . $m[1];
        }

        if (!preg_match('/^0\d{9}$/', $cleaned)) {
            return null;
        }

        $prefix = substr($cleaned, 0, 3);
        if (!isset(self::MOBILE_PREFIXES[$prefix]) && !isset(self::LANDLINE_AREAS[$prefix])) {
            return null;
        }

        return $cleaned;
    }

    /**
     * Convert a valid phone number to E.164 international format (+94xxxxxxxxx).
     */
    public static function toInternational(string $phone): ?string
    {
        $normalized = self::normalize($phone);
        if ($normalized === null) {
            return null;
        }

        return '+94' . substr($normalized, 1);
    }

    /**
     * Format a valid phone number.
     *
     * Supported styles:
     *  - 'local':         077 123 4567
     *  - 'international': +94 77 123 4567
     *  - 'e164':          +94771234567
     *  - 'compact':       0771234567
     */
    public static function format(string $phone, string $style = 'local'): ?string
    {
        $normalized = self::normalize($phone);
        if ($normalized === null) {
            return null;
        }

        return match ($style) {
            'local'         => substr($normalized, 0, 3) . ' ' . substr($normalized, 3, 3) . ' ' . substr($normalized, 6, 4),
            'international' => '+94 ' . substr($normalized, 1, 2) . ' ' . substr($normalized, 3, 3) . ' ' . substr($normalized, 6, 4),
            'e164'          => '+94' . substr($normalized, 1),
            'compact'       => $normalized,
            default         => $normalized,
        };
    }

    /**
     * Detect the network operator (Dialog, Mobitel, Hutch, Airtel, or SLT).
     */
    public static function getNetwork(string $phone): ?string
    {
        $normalized = self::normalize($phone);
        if ($normalized === null) {
            return null;
        }

        $prefix = substr($normalized, 0, 3);

        if (isset(self::MOBILE_PREFIXES[$prefix])) {
            return self::MOBILE_PREFIXES[$prefix];
        }

        if (isset(self::LANDLINE_AREAS[$prefix])) {
            return 'SLT';
        }

        return null;
    }

    /**
     * Get the geographic area name for a landline number (null for mobile or invalid).
     */
    public static function getArea(string $phone): ?string
    {
        $normalized = self::normalize($phone);
        if ($normalized === null) {
            return null;
        }

        $prefix = substr($normalized, 0, 3);

        return self::LANDLINE_AREAS[$prefix] ?? null;
    }

    /**
     * Check if number is a valid Sri Lanka mobile number.
     */
    public static function isMobile(string $phone): bool
    {
        $normalized = self::normalize($phone);
        if ($normalized === null) {
            return false;
        }

        return isset(self::MOBILE_PREFIXES[substr($normalized, 0, 3)]);
    }

    /**
     * Check if number is a valid Sri Lanka landline number.
     */
    public static function isLandline(string $phone): bool
    {
        $normalized = self::normalize($phone);
        if ($normalized === null) {
            return false;
        }

        return isset(self::LANDLINE_AREAS[substr($normalized, 0, 3)]);
    }

    /**
     * Parse phone number into full details.
     *
     * @return array{
     *     valid: bool,
     *     normalized: string|null,
     *     international: string|null,
     *     formatted: string|null,
     *     network: string|null,
     *     type: string|null,
     *     area: string|null
     * }
     */
    public static function parse(string $phone): array
    {
        $normalized = self::normalize($phone);

        if ($normalized === null) {
            return [
                'valid'         => false,
                'normalized'    => null,
                'international' => null,
                'formatted'     => null,
                'network'       => null,
                'type'          => null,
                'area'          => null,
            ];
        }

        return [
            'valid'         => true,
            'normalized'    => $normalized,
            'international' => '+94' . substr($normalized, 1),
            'formatted'     => self::format($normalized, 'local'),
            'network'       => self::getNetwork($normalized),
            'type'          => self::isMobile($normalized) ? 'mobile' : 'landline',
            'area'          => self::getArea($normalized),
        ];
    }
}

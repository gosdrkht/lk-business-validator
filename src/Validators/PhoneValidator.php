<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka Phone Number Validator
 *
 * Supports local (07x, 01x, 011) and international (+94) formats.
 */
class PhoneValidator
{
    // Mobile network prefixes
    const DIALOG   = ['070', '071', '072', '074', '075', '076', '077', '078'];
    const MOBITEL  = ['071', '072'];  // shared prefix range
    const HUTCH    = ['078'];
    const AIRTEL   = ['075'];
    const SLT      = ['011', '021', '022', '023', '024', '025', '026', '027', '031', '032', '033', '034', '035', '036', '037', '038', '041', '045', '047', '051', '052', '054', '055', '057', '063', '065', '066', '067'];

    /**
     * Validate a Sri Lanka phone number.
     */
    public static function validate(string $phone): bool
    {
        $normalized = self::normalize($phone);
        return $normalized !== null;
    }

    /**
     * Normalize phone number to local format (0xxxxxxxxx).
     */
    public static function normalize(string $phone): ?string
    {
        $phone = preg_replace('/[\s\-\(\)\.]+/', '', $phone);

        // +94xxxxxxxxx → 0xxxxxxxxx
        if (preg_match('/^\+94(\d{9})$/', $phone, $m)) {
            $phone = '0' . $m[1];
        }

        // 94xxxxxxxxx → 0xxxxxxxxx
        if (preg_match('/^94(\d{9})$/', $phone, $m)) {
            $phone = '0' . $m[1];
        }

        // Must be 10 digits starting with 0
        if (!preg_match('/^0\d{9}$/', $phone)) {
            return null;
        }

        return $phone;
    }

    /**
     * Convert to international format (+94xxxxxxxxx).
     */
    public static function toInternational(string $phone): ?string
    {
        $normalized = self::normalize($phone);
        if (!$normalized) return null;
        return '+94' . substr($normalized, 1);
    }

    /**
     * Detect the network operator.
     */
    public static function getNetwork(string $phone): ?string
    {
        $normalized = self::normalize($phone);
        if (!$normalized) return null;

        $prefix3 = substr($normalized, 0, 3);

        if (in_array($prefix3, ['070', '074', '076', '077'])) return 'Dialog';
        if (in_array($prefix3, ['071', '072'])) return 'Dialog/Mobitel';
        if ($prefix3 === '075') return 'Airtel';
        if ($prefix3 === '078') return 'Hutch';
        if ($prefix3 === '079') return 'Hutch';
        if (in_array($prefix3, self::SLT)) return 'SLT';

        return 'Unknown';
    }

    /**
     * Check if number is a mobile number.
     */
    public static function isMobile(string $phone): bool
    {
        $normalized = self::normalize($phone);
        if (!$normalized) return false;

        return preg_match('/^07\d{8}$/', $normalized) === 1;
    }

    /**
     * Check if number is a landline.
     */
    public static function isLandline(string $phone): bool
    {
        $normalized = self::normalize($phone);
        if (!$normalized) return false;

        return !self::isMobile($phone);
    }

    /**
     * Parse phone number into details.
     *
     * @return array{valid: bool, normalized: string|null, international: string|null, network: string|null, type: string|null}
     */
    public static function parse(string $phone): array
    {
        $normalized = self::normalize($phone);

        if (!$normalized) {
            return ['valid' => false, 'normalized' => null, 'international' => null, 'network' => null, 'type' => null];
        }

        return [
            'valid'         => true,
            'normalized'    => $normalized,
            'international' => self::toInternational($phone),
            'network'       => self::getNetwork($phone),
            'type'          => self::isMobile($phone) ? 'mobile' : 'landline',
        ];
    }
}

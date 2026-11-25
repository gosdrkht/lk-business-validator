<?php

namespace Trishanapp\LkBusinessValidator\Validators;

/**
 * Sri Lanka Bank Validator (CBSL Bank Codes, SWIFT/BIC, Branch Codes & Account Numbers)
 *
 * Validates official Central Bank of Sri Lanka (CBSL) 4-digit bank codes,
 * Sri Lanka SWIFT/BIC codes (country code 'LK'), 3-digit branch codes,
 * and Sri Lanka bank account number formats.
 */
class BankValidator
{
    /** @var array<string, string> Official CBSL 4-digit bank code => Bank name */
    public const BANKS = [
        '7010' => 'Bank of Ceylon',
        '7038' => 'Standard Chartered Bank',
        '7047' => 'Citibank N.A.',
        '7056' => 'Commercial Bank of Ceylon PLC',
        '7074' => 'Habib Bank Ltd',
        '7083' => 'Hatton National Bank PLC',
        '7092' => 'Hongkong and Shanghai Banking Corporation (HSBC)',
        '7108' => 'Indian Bank',
        '7117' => 'Indian Overseas Bank',
        '7135' => 'People\'s Bank',
        '7144' => 'State Bank of India',
        '7162' => 'Nations Trust Bank PLC',
        '7205' => 'Deutsche Bank AG',
        '7214' => 'National Development Bank PLC (NDB)',
        '7269' => 'MCB Bank Ltd',
        '7278' => 'Sampath Bank PLC',
        '7287' => 'Seylan Bank PLC',
        '7296' => 'Public Bank Berhad',
        '7302' => 'Union Bank of Colombo PLC',
        '7311' => 'Pan Asia Banking Corporation PLC',
        '7384' => 'ICICI Bank Ltd',
        '7454' => 'DFCC Bank PLC',
        '7463' => 'Amana Bank PLC',
        '7472' => 'Axis Bank Ltd',
        '7481' => 'Cargills Bank PLC',
        '7719' => 'National Savings Bank (NSB)',
        '7728' => 'Sanasa Development Bank PLC (SDB)',
        '7737' => 'Housing Development Finance Corporation Bank (HDFC)',
        '7746' => 'Regional Development Bank (RDB)',
        '7755' => 'Sri Lanka Savings Bank',
        '7764' => 'State Mortgage & Investment Bank (SMIB)',
    ];

    /**
     * Validate a 4-digit CBSL Sri Lanka bank code.
     */
    public static function validateBankCode(string|int $code): bool
    {
        $code = trim((string) $code);

        return isset(self::BANKS[$code]);
    }

    /**
     * Get the bank name for a valid CBSL bank code.
     */
    public static function getBankName(string|int $code): ?string
    {
        $code = trim((string) $code);

        return self::BANKS[$code] ?? null;
    }

    /**
     * Validate a Sri Lanka SWIFT/BIC code (8 or 11 characters, country code 'LK').
     *
     * Format: AAAALKBB or AAAALKBBXXX
     *   - 4 letters: Bank code
     *   - 2 letters: Country code ('LK')
     *   - 2 alphanumeric: Location code
     *   - 3 alphanumeric (optional): Branch code
     */
    public static function validateSwift(string $swift): bool
    {
        $swift = strtoupper(trim($swift));

        return (bool) preg_match('/^[A-Z]{4}LK[A-Z0-9]{2}([A-Z0-9]{3})?$/', $swift);
    }

    /**
     * Validate a 3-digit Sri Lanka bank branch code (e.g. '001', '123').
     */
    public static function validateBranchCode(string|int $branchCode): bool
    {
        $branchCode = trim((string) $branchCode);

        return (bool) preg_match('/^\d{3}$/', $branchCode);
    }

    /**
     * Validate a Sri Lanka bank account number (8–16 digits, optional spaces or hyphens).
     */
    public static function validateAccountNumber(string $accountNumber): bool
    {
        $cleaned = preg_replace('/[\s\-]+/', '', trim($accountNumber));

        return (bool) preg_match('/^\d{8,16}$/', (string) $cleaned);
    }

    /**
     * Parse a CBSL bank code into details.
     *
     * @return array{valid: bool, code: string|null, bank_name: string|null}
     */
    public static function parseBankCode(string|int $code): array
    {
        $normalized = trim((string) $code);

        if (!self::validateBankCode($normalized)) {
            return [
                'valid'     => false,
                'code'      => null,
                'bank_name' => null,
            ];
        }

        return [
            'valid'     => true,
            'code'      => $normalized,
            'bank_name' => self::BANKS[$normalized],
        ];
    }
}

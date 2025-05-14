<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class EncryptionHelper
{
    // A titkosítandó mezők
    public static array $fieldsToEncrypt = [
        'firstName',
        'lastName',
        'phoneNumber',
        'email',
        'country',
        'city',
        'jobTitle',
        'introduce',
        'age',
        'ethnic',
    ];

    /**
     * Titkosítás
     */
    public static function encryptFields(array $data): array
    {
        foreach (self::$fieldsToEncrypt as $field) {
            if (array_key_exists($field, $data) && ! is_null($data[$field])) {
                $data[$field] = Crypt::encryptString($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Visszafejtés
     */
    public static function decryptFields(array $data): array
    {
        foreach (self::$fieldsToEncrypt as $field) {
            if (array_key_exists($field, $data) && ! is_null($data[$field])) {
                try {
                    $data[$field] = Crypt::decryptString($data[$field]);
                } catch (\Exception $e) {
                    // Ha nem titkosított, hagyd érintetlenül
                }
            }
        }

        return $data;
    }
}

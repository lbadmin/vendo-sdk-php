<?php
namespace VendoSdk\Crypto;

class Aes128Ecb
{
    const ALGO = 'AES-128-ECB';

    /**
     * Encrypts a string using AES128.
     * This method generates exactly the same output as MySQL's AES_ENCRYPT() function.
     *
     * @param string $uncryptedData
     * @param string $key The key to use in the encryption.
     * @return string The result is binary
     */
    public static function encrypt(string $uncryptedData, string $key): string
    {
        return openssl_encrypt($uncryptedData, self::ALGO, $key, OPENSSL_RAW_DATA);
    }

    /**
     * Decrypts a string that was encrypted using AES128
     * This method generates exactly the same output as MySQL's AES_DECRYPT() function.
     *
     * @param string $encryptedData
     * @param string $key
     * @return string
     */
    public static function decrypt(string $encryptedData, string $key): string
    {
        return openssl_decrypt($encryptedData, self::ALGO, $key, OPENSSL_RAW_DATA);
    }
}
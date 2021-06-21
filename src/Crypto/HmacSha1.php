<?php
namespace VendoSdk\Crypto;

use VendoSdk\Exception;

/**
 * Calculates a signature using HMAC-SHA1 algorithm
 */
class HmacSha1
{
    /** @var string The hashing key */
    protected $key;

    /**
     * HmacSha1 constructor.
     *
     * @param string $key The hashing key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Hashes $data and returns the hash
     *
     * @param string $data
     * @return string
     * @throws Exception
     */
    public function getHash(string $data): string
    {
        if (empty($this->key)) {
            throw new Exception('They hashing key was not set');
        }
        return $this->_hmacSha1($data, $this->key);
    }

    /**
     * Creates a HMAC-SHA1 signature of the data signed with the secret key and returns it as a base-64 encoded string
     * with '=' characters removed (suitable for url parameter).
     *
     * @param string $data
     * @param string $key
     * @return string
     */
    protected function _hmacSha1(string $data, string $key): string
    {
        // Fixing key length if needed (SHA-1 blocksize = 512 bits = 64 * 8)
        if (strlen($key) > 64) {
            $key = sha1($key, true);
        } else {
            $key = str_pad($key, 64, chr(0));
        }

        $ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
        $opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));
        $hash = sha1($opad . sha1($ipad . $data, true), true);
        $hash = $this->_base64UrlEncode($hash);

        return $hash;
    }

    /**
     * Encodes the given data in a URL-compatible Base64 string.  Compared to * normal Base64, the URL version removes
     * any = characters and replaces + with - and / with _.
     *
     * @param string $data  the data to encode
     * @return string
     */
    private function _base64UrlEncode(string $data): string
    {
        $data = base64_encode($data);
        $data = str_replace('=', '', $data);
        $data = str_replace('+', '-', $data);
        $data = str_replace('/', '_', $data);

        return $data;
    }
}
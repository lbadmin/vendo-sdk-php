<?php

namespace VendoSdk\Util;

use League\Uri;
use VendoSdk\Crypto\HmacSha1;

/**
 * A helper class to generate Vendo's signed URLs
 */
class Signature
{
    /** @var HmacSha1 */
    protected $hasher;

    /**
     * Signature constructor.
     * @param string $sharedSecret
     */
    public function __construct(string $sharedSecret)
    {
        $this->hasher = new HmacSha1($sharedSecret);
    }

    /**
     * Validates data against a signature
     *
     * @param string $data
     * @param string $signature
     * @return bool
     * @throws \VendoSdk\Exception
     */
    public function isValid(string $data, string $signature): bool
    {
        return $this->isValidSignature($data, $signature);
    }

    /**
     * Validates a url which includes a signature parameter
     *
     * Only the path and query string parts of the url are checked for authenticity. The host, port and schema etc. are
     * ignored. This method also works with the URLs that only contains path and query segments.
     *
     * If the url contains an expiry parameter, it is also checked
     *
     * @param string $url
     * @param string $signatureParamName
     * @param string $expiryParamName
     * @return bool
     * @throws \VendoSdk\Exception
     */
    public function isValidUrl(string $url, string $signatureParamName = 'signature', string $expiryParamName = 'expires'): bool
    {
        $urlObject = Uri\Http::createFromString($url);

        $path = $urlObject->getPath();
        $query = $urlObject->getQuery();

        $params = [];
        parse_str($query, $params);

        if (!empty($expiryParamName) && !empty($params[$expiryParamName]) && (int)$params[$expiryParamName] < time()) {
            $isValid = false;
        } elseif (isset($params[$signatureParamName])) {
            $signature = $params[$signatureParamName];
            unset($params[$signatureParamName]);

            $components = [];
            if ($path > '') {
                $components['path'] = $path;
            }
            if (!empty($params)) {
                $components['query'] = http_build_query($params);
            }

            $data = (string)Uri\Http::createFromComponents($components);

            // Create secondary raw data string similar to the old signature helper to provide backwards compatibility
            $data2 = preg_replace('/(\b&?signature=[^&$]+|(https?)?\:\/\/[^\/]+)/', '', $url);

            $isValid = $this->isValid($data, $signature) || $this->isValid($data2, $signature);
        }
        return $isValid ?? false;
    }

    /**
     * Returns the signature of the data
     *
     * @param string $data
     * @return string
     * @throws \VendoSdk\Exception
     */
    public function getSignature(string $data): string
    {
        return $this->hasher->getSignature($data);
    }

    /**
     * Returns the url with embedded signature parameter
     *
     * @param string $url
     * @param string $signatureParamName
     * @return string
     * @throws \VendoSdk\Exception
     */
    public function sign(string $url, string $signatureParamName = 'signature'): string
    {
        $urlParser = new Uri\UriString();

        $allComponents = $urlParser->parse($url);

        $signedComponents = [];

        $path = $allComponents['path'];
        $query = $allComponents['query'];
        if ($path > '') {
            $signedComponents['path'] = $path;
        }
        if ($query > '') {
            $params = [];
            parse_str($query, $params);
            $signedComponents['query'] = http_build_query($params);
        }

        $data = (string)Uri\Http::createFromComponents($signedComponents);

        $signature = $this->getSignature($data);

        $params = [];
        parse_str($allComponents['query'], $params);
        $params[$signatureParamName] = $signature;
        $allComponents['query'] = http_build_query($params);
        $signedUrl = $urlParser->build($allComponents);

        return (string)$signedUrl;
    }

    /**
     * Checks if the specified signature matches the data's signature
     *
     * @param string $data
     * @param string $signature
     * @return bool
     * @throws \VendoSdk\Exception
     */
    public function isValidSignature(string $data, string $signature): bool
    {
        return $signature == $this->hasher->getSignature($data);
    }

}
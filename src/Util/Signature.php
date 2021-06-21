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
     * @param string $data Example http://dummy/?test=test&foo=bar
     * @param string $signature
     * @return bool
     * @throws \VendoSdk\Exception
     */
    public function isValid(string $data, string $signature): bool
    {
        return $signature == $this->getSignature($data);
    }

    /**
     * Validates a url which includes a signature parameter
     *
     * Only the path and query string parts of the url are checked for authenticity. The host, port and schema etc. are
     * ignored. This method also works with the URLs that only contains path and query segments.
     *
     * If the url contains an "expires" parameter, it is also checked
     *
     * @param string $url
     * @return bool
     * @throws \VendoSdk\Exception
     */
    public function isValidUrl(string $url): bool
    {
        $urlObject = Uri\Http::createFromString($url);

        $path = $urlObject->getPath();
        $query = $urlObject->getQuery();

        $params = [];
        parse_str($query, $params);

        if (!empty($params['expires']) && (int)$params['expires'] < time()) {
            $isValid = false;
        } else {
            if (!empty($params['signature'])) {
                $signature = $params['signature'];
                unset($params['signature']);
            }

            $components = [];
            if (!empty($path)) {
                $components['path'] = $path;
            }
            if (!empty($params)) {
                $components['query'] = http_build_query($params);
            }

            $data = (string)Uri\Http::createFromComponents($components);
            $isValid = $this->isValid($data, $signature);
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
        return $this->hasher->getHash($data);
    }

    /**
     * Returns the url with embedded signature parameter
     *
     * @param string $url
     * @return string
     * @throws \VendoSdk\Exception
     */
    public function sign(string $url): string
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
        $params['signature'] = $signature;
        $allComponents['query'] = http_build_query($params);
        $signedUrl = $urlParser->build($allComponents);

        return (string)$signedUrl;
    }
}
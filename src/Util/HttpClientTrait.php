<?php
namespace VendoSdk\Util;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\UriInterface;
use VendoSdk\Vendo;

trait HttpClientTrait
{
    /** @var HttpClient */
    protected $httpClient;

    /**
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        if (empty($this->httpClient)) {
            $this->httpClient = new HttpClient();
        }
        return $this->httpClient;
    }

    /**
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $method
     * @param UriInterface|string $uri
     * @param array $headers
     * @param ?string $body
     * @param string $version
     * @return Request
     */
    public function getHttpRequest(
        string $method,
        string $uri,
        array $headers = [],
        ?string $body = null,
        string $version = '1.1'
    ): Request
    {
        $headers['X-VENDO-PHP-SDK-VERSION'] = Vendo::SDK_VERSION;
        return new Request($method, $uri, $headers, $body, $version);
    }

}

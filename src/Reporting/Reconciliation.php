<?php
namespace VendoSdk\Reporting;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use VendoSdk\Exception;
use VendoSdk\Reporting\Response\Parser;
use VendoSdk\Reporting\Response\RowElement;
use VendoSdk\Url\Base;

/**
 * Queries Vendo's Reconciliation API.
 *
 * @package VendoSdk\Reporting
 */
class Reconciliation extends Base
{
    const FORMAT_CSV = 0;
    const FORMAT_XML = 1;

    protected $rawResponse;

    protected $httpClient;

    public function setMerchantId(int $vendoMerchantId): void
    {
        $this->merchantID = $vendoMerchantId;
    }
    public function getMerchantId(): int
    {
        return $this->merchantID;
    }

    public function setStartDate(\DateTime $startDate): void
    {
        $this->startDate = $startDate->format('Y-m-d');
    }
    public function getStartDate(): ?\DateTime
    {
        if (!empty($this->startDate)) {
           return \DateTime::createFromFormat('Y-m-d', $this->startDate);
        }
        return null;
    }

    public function setEndDate(\DateTime $endDate): void
    {
        $this->endDate = $endDate->format('Y-m-d');
    }
    public function getEndDate(): ?\DateTime
    {
        if (!empty($this->endDate)) {
            return \DateTime::createFromFormat('Y-m-d', $this->endDate);
        }
        return null;
    }

    /**
     * @param int[] $vendoSiteIds
     */
    public function setSiteIds(array $vendoSiteIds): void
    {
        $this->siteIDs = implode(',', $vendoSiteIds);
    }
    public function getSiteIds(): string
    {
        return explode(',', $this->siteIDs);
    }

    /**
     * Valid values are 1 and 0. 1 = XML and 0 = CSV
     *
     * @param int $format
     */
    public function setFormat(int $format): void
    {
        $this->format = $format;
    }
    public function getFormat(): int
    {
        return $this->format;
    }

    /**
     * @inheritdoc
     */
    public function getBaseUrl(): string
    {
        return parent::getBaseUrl() . '/api/reconciliation';
    }

    /**
     * @inheritdoc
     */
    public function __construct(string $sharedSecret)
    {
        parent::__construct($sharedSecret);
        $this->rawResponse = null;
        $this->format = self::FORMAT_XML;
    }

    /**
     * Returns the parsed response or null if the data set that was returned by Vendo is empty.
     * Throws \VendoSdk\Exception if the API returned an error.
     * You must set $reconciliation->format = ; in order to use this method
     *
     * @return ?RowElement[]
     * @throws Exception
     * @throws \Exception
     */
    public function getTransactions()
    {
        $rawResponse = $this->getRawResponse();
        if (empty($rawResponse)) {
            throw new Exception('Your must call $reconciliation->postRequest() first.');
        }
        if ($this->format != self::FORMAT_XML) {
            throw new Exception('You must set $reconciliation->format = Reconciliation::FORMAT_XML' .
                'before calling $reconciliation->postRequest() in order to use this method'
            );
        }
        $xml = new Parser($rawResponse);
        if (!empty($xml->error)) {
            throw new Exception('Vendo\'s Reconciliation API returned this error: (' . $xml->error['code']
                . ') ' . $xml->error . '"'
            );
        }

        return $xml->header->transactionCount > 0 ? $xml->body->row : null;
    }

    /**
     * Returns the raw response that was returned by Vendo
     * @return ?string
     */
    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }

    public function getHttpClient(): HttpClient
    {
        if (empty($this->httpClient)) {
            $this->httpClient = new HttpClient();
        }
        return $this->httpClient;
    }

    public function setHttpClient(HttpClient $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Queries Vendo's Reconciliation API. Returns true if the request was successful.
     *
     * @return bool
     * @throws Exception
     * @throws GuzzleException
     * @throws \Exception
     */
    public function postRequest(): bool
    {
        $url = $this->getSignedUrl();
        $client = $this->getHttpClient();
        $headers = [
            'VENDO_PHP_SDK_VERSION' => include __DIR__ . '/../../sdk-version.php',
        ];
        $request = new Request('POST', $url, $headers);
        $response = $client->send($request);

        $httpStatus = $response->getStatusCode();
        if ($httpStatus == 200) {
            $this->rawResponse = $response->getBody();
            return true;
        } else {
            throw new Exception('The HTTP request failed. Status code:' . $httpStatus .
                ' Http message:' . $response->getReasonPhrase() ?? '-no message-'
            );
        }
    }

    /**
     * @inheritdoc
     */
    protected function setAllowedUrlParameters(): void {
        $this->allowedUrlParams = [
            'merchantID',
            'startDate',
            'endDate',
            'siteIDs',
            'format',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function setUrlParametersValidators(): void {
        $this->urlParamValidators['startDate'] = function ($value) {
            if ((bool)preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $value) == false ) {
                throw new Exception('This date is not valid: ' . $value);
            }
        };
        $this->urlParamValidators['endDate'] = $this->urlParamValidators['startDate'];
        $this->urlParamValidators['siteIDs'] = function ($value) {
            array_map(function($value) {
                if (!is_numeric($value)) {
                    throw new Exception("This Site ID is invalid: " . $value);
                }
            }, explode(',', $value));
        };
        $this->urlParamValidators['format'] = function ($value) {
            if ($value != Reconciliation::FORMAT_CSV && $value != Reconciliation::FORMAT_XML) {
                throw new Exception("This format is invalid: " . $value);
            }
        };
        $this->urlParamValidators['merchantID'] = function ($value) {
            if (!is_numeric($value)) {
                throw new Exception("This merchantID is invalid: " . $value);
            }
        };
    }
}
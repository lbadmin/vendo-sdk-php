<?php

namespace VendoSdk\Url;

use VendoSdk\Crypto\Aes128Ecb;
use VendoSdk\Exception;

/**
 * Use this class to generate Standard Join Links
 * @see https://docs.vendoservices.com/docs/one-clicks
 *
 * @method setSubscription(int $vendoSubscriptionId) We recommend to pass in the Affiliate ID so Vendo can track risk and also provide reporting on this value. Set 0 for organic/search engine referral.
 * @method string getSubscription()
 * @method setOffers(array $offers) The array of Vendo Oneclick Offers IDs. Optional.
 * @method array getOffers()
 * @method setOffer(int $selectedOneclickOfferId) The offer that must be preselected. Mandatory.
 * @method int getOffer()
 * @method setSuccessUrl(string $url) The user will be redirected to this URL if the transaction was successful. You can use placeholders to get transaction details @see https://docs.vendoservices.com/docs/standard-join-link#redirection-parameters
 * @method string getSuccessUrl()
 * @method setDeclineUrl(string $url) The user will be redirected to this URL if the transaction failed
 * @method string getDeclineUrl()
 * @method setRef(string $merchantReference) This is a reference (a.k.a. merchant reference) that will be posted back to you on every postback
 * @method string getRef()
 * @method setSiteName(string $siteName) Set this only when working with whitelabed sites.
 * @method string getSiteName()
 * @method setSiteUrl(string $siteUrl) Set this only when working with whitelabed sites.
 * @method string getSiteUrl()
 */
class Oneclick extends Base
{
    public function getBaseUrl(): string
    {
        return parent::getBaseUrl() . '/v/oneclick';
    }

    /**
     * @param string $paramName
     * @param mixed $paramValue
     * @throws Exception
     */
    public function __set(string $paramName, $paramValue): void
    {
        if ($paramName === 'offers') {
            if (is_array($paramValue)) {
                $paramValue = implode(',', $paramValue);
            }
        }
        parent::__set($paramName, $paramValue);
    }

    /**
     * @param string $paramName
     * @return false|mixed|string[]|null
     * @throws Exception
     */
    public function __get(string $paramName)
    {
        $paramValue = parent::__get($paramName);
        if ($paramName === 'offers') {
            if (is_array($paramValue)) {
                $paramValue = explode(',', $paramValue);
            }
        }
        return $paramValue;
    }

    protected function setUrlParametersValidators(): void
    {
        parent::setUrlParametersValidators();
        $this->urlParamValidators['site_url'] = $this->urlParamValidators['success_url'];
    }

    protected function setAllowedUrlParameters(): void
    {
        $this->allowedUrlParams = [
            'subscription',
            'offer',
            'offers',
            'decline_url',
            'success_url',
            'ref',
            'site_name',
            'site_url',
            'expires',
        ];
    }
}

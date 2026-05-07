<?php

namespace VendoSdk\Url;

use VendoSdk\Crypto\Aes128Ecb;
use VendoSdk\Exception;
use VendoSdk\Vendo;

/**
 * Use this class to generate Custom Offer links (on-the-fly billing amounts and schedule via URL parameters).
 *
 * Catalog offer parameters are intentionally omitted: use {@see Join} or {@see Oneclick} when passing
 * `offers`, `selected_offer`, `offer`, or `offers`.
 *
 * @see https://docs.vendoservices.com/docs/custom-offer-link
 *
 * Custom billing parameters
 * @method setSite(int $vendoSiteId)
 * @method int getSite()
 * @method setType(string $type) normal or oneclick
 * @method string getType()
 * @method setBillingScheduleType(string $type) trial, membership, lifetime, or token
 * @method string getBillingScheduleType()
 * @method setInitialAmount(float $amount) USD amount for initial price point when applicable
 * @method float getInitialAmount()
 * @method setInitialDuration(int $days) Duration in days of the initial period
 * @method int getInitialDuration()
 * @method setRebillAmount(float $amount) USD amount for recurring rebills when applicable
 * @method float getRebillAmount()
 * @method setRebillDuration(int $days) Days between recurring rebills
 * @method int getRebillDuration()
 * @method setTokenAmount(float $amount) USD amount for token pricing when applicable
 * @method float getTokenAmount()
 * @method setTokenQuantity(int $quantity) Total quantity of tokens when billing_schedule_type is token
 * @method int getTokenQuantity()
 *
 * Standard Join-style parameters (same path as {@see Join}, excluding catalog offer IDs)
 * @method setPage(string $page)
 * @method string getPage()
 * @method setCountry(string $country)
 * @method string getCountry()
 * @method setLanguage(string $language)
 * @method string getLanguage()
 * @method setBillingCurrency(string $currency) USD, EUR or GBP. URL must be signed.
 * @method string getBillingCurrency()
 * @method setPm(string $method) Optional. Pre-select payment method (`pm`). Use {@see Vendo::PAYMENT_METHOD_*} constants. If omitted, all payment methods available for the geolocated country are shown.
 * @method string getPm()
 * @method setEmail(string $email)
 * @method string getEmail()
 * @method setUsername(string $username)
 * @method string getUsername()
 * @method setPassword(string $password)
 * @method string getPassword()
 * @method setEmailHide(int $hide)
 * @method int getEmailHide()
 * @method setUsernameHide(int $hide)
 * @method int getUsernameHide()
 * @method setPasswordHide(int $hide)
 * @method int getPasswordHide()
 * @method setSubscription(int $subscriptionId) Required context for type=oneclick flows
 * @method string getSubscription()
 * @method setFirstname(string $firstname)
 * @method string getFirstname()
 * @method setLastname(string $lastname)
 * @method string getLastname()
 * @method setStreet(string $street)
 * @method string getStreet()
 * @method setCity(string $city)
 * @method string getCity()
 * @method setZip(string $zip)
 * @method string getZip()
 * @method setState(string $state)
 * @method string getState()
 * @method setXsalesMax(int $xsalesMax)
 * @method int getXsalesMax()
 * @method setXsales(array $xsalesOfferIds)
 * @method array getXsales()
 * @method setXsaleRef(array $xsalesRefs)
 * @method array getXsaleRef()
 * @method setSuccessUrl(string $url)
 * @method string getSuccessUrl()
 * @method setDeclineUrl(string $url)
 * @method string getDeclineUrl()
 * @method setLogindataHide(int $hide)
 * @method string getLogindataHide()
 * @method setRef(string $merchantReference)
 * @method string getRef()
 * @method setAffiliateId(int $affiliateId)
 * @method string getAffiliateId()
 * @method setCampaignId(int $campaignId)
 * @method int getCampaignId()
 * @method setProgramId(int $programId)
 * @method int getProgramId()
 * @method setSiteName(string $siteName)
 * @method string getSiteName()
 * @method setSiteUrl(string $siteUrl)
 * @method string getSiteUrl()
 */
class CustomOffer extends Base
{
    public function getBaseUrl(): string
    {
        return parent::getBaseUrl() . '/v/custom-offer';
    }

    /**
     * @param string $paramName
     * @param mixed $paramValue
     * @throws Exception
     */
    public function __set(string $paramName, $paramValue): void
    {
        if ($paramName === 'password') {
            $paramValue = Aes128Ecb::encrypt($paramValue, $this->getSharedSecret());
            parent::__set('password_encrypted', 1);
        }
        if ($paramName === 'xsales' || $paramName === 'xsale_ref') {
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
        if ($paramName === 'xsales' || $paramName === 'xsale_ref') {
            if (is_string($paramValue) && $paramValue !== '') {
                $paramValue = explode(',', $paramValue);
            }
        } elseif ($paramName === 'password') {
            if (!empty($this->urlParamValues[$paramName])) {
                $paramValue = Aes128Ecb::decrypt($this->urlParamValues[$paramName], $this->getSharedSecret());
            }
        }
        return $paramValue;
    }

    protected function setUrlParametersValidators(): void
    {
        parent::setUrlParametersValidators();

        $this->urlParamValidators['country'] = function ($value) {
            if (strlen($value) != 2) {
                throw new Exception('The country parameter must have exactly two characters, example: US.');
            }
        };
        $this->urlParamValidators['page'] = function ($value) {
            if ($value !== 'join' && $value !== 'prejoin') {
                throw new Exception('The page parameter must be join or prejoin.');
            }
        };
        $this->urlParamValidators['site_url'] = $this->urlParamValidators['success_url'];
        $this->urlParamValidators['billing_currency'] = function ($value) {
            if (!in_array($value, Vendo::getAllowedBillingCurrencies(), true)) {
                throw new Exception(sprintf(
                    'billing_currency must be one of: %s.',
                    implode(', ', Vendo::getAllowedBillingCurrencies())
                ));
            }
        };
        $this->urlParamValidators['pm'] = function ($value) {
            if (!in_array($value, Vendo::getAllowedHostedCheckoutPaymentMethods(), true)) {
                throw new Exception(sprintf(
                    'pm must be one of: %s.',
                    implode(', ', Vendo::getAllowedHostedCheckoutPaymentMethods())
                ));
            }
        };
        $this->urlParamValidators['type'] = function ($value) {
            if (!in_array($value, ['normal', 'oneclick'], true)) {
                throw new Exception('The type parameter must be normal or oneclick.');
            }
        };
        $this->urlParamValidators['billing_schedule_type'] = function ($value) {
            if (!in_array($value, ['trial', 'membership', 'lifetime', 'token'], true)) {
                throw new Exception('billing_schedule_type must be trial, membership, lifetime, or token.');
            }
        };
    }

    protected function setAllowedUrlParameters(): void
    {
        $this->allowedUrlParams = [
            'site',
            'type',
            'billing_schedule_type',
            'initial_amount',
            'initial_duration',
            'rebill_amount',
            'rebill_duration',
            'token_amount',
            'token_quantity',
            'page',
            'country',
            'language',
            'billing_currency',
            'pm',
            'email',
            'username',
            'password',
            'password_encrypted',
            'email_hide',
            'username_hide',
            'password_hide',
            'subscription',
            'firstname',
            'lastname',
            'street',
            'city',
            'zip',
            'state',
            'xsales_max',
            'xsales',
            'xsale_ref',
            'decline_url',
            'success_url',
            'logindata_hide',
            'ref',
            'affiliate_id',
            'campaign_id',
            'program_id',
            'site_name',
            'site_url',
            'expires',
        ];
    }
}

<?php

namespace VendoSdk\Url;

use VendoSdk\Crypto\Aes128Ecb;
use VendoSdk\Exception;

/**
 * Use this class to generate Standard Join Links
 * @see https://docs.vendoservices.com/docs/standard-join-link
 *
 * @method setSite(int $vendoSiteId)
 * @method int getSite()
 * @method setPage(string $page)
 * @method string getPage()
 * @method setCountry(string $country)
 * @method string getCountry()
 * @method setLanguage(string $language)
 * @method string getLanguage()
 * @method setEmail(string $email)
 * @method string getEmail()
 * @method setUsername(string $username)
 * @method string getUsername()
 * @method setPassword(string $password)
 * @method string getPassword()
 * @method setEmailHide(int $hide) Set to 1 to hide the email field. If you hide it then you must $this->setEmail('example@example.com')
 * @method int getEmailHide()
 * @method setPasswordHide(int $hide) Set to 1 to hide the password field. If you hide it then you must $this->setPassword('a_secret')
 * @method int getPasswordHide()
 * @method setSubscription(int $subscriptionId) If you set the Vendo Subscription ID then Vendo will use the user details from the subscription to reduce the number of fields in the payment page.
 * @method string getSubscription()
 * @method setFirstname(string $firstname)
 * @method string getFirstname()
 * @method setLastname(string $lastname)
 * @method string getLastname()
 * @method setStreet(string $street)
 * @method string getStreet()
 * @method setCity(string $city)
 * @method string getCity()
 * @method setState(string $state)
 * @method string getState()
 * @method setOffers(array $offers) The array of Vendo Offers IDs. These will get displayed in the product or payment page.
 * @method array getOffers()
 * @method setSelectedOffer(int $selectedOfferId) The offer that must be preselected.
 * @method int getSelectedOffer()
 * @method setXsalesMax(int $xsalesMax) How many xsales should be displayed? Valid values are 1 or 2
 * @method int getXsalesMax()
 * @method setXsales(array $xsalesOfferIds) The array of Cross Sales Offer IDs
 * @method int getXsales()
 * @method setXsaleRef(array $xsalesRefs) The array of Cross Sales Offer References. You must pass one ref per xsale offer id set in $this->setXsales(array)
 * @method array getXsaleRef()
 * @method setSuccessUrl(string $url) The user will be redirected to this URL if the transaction was successful. You can use placeholders to get transaction details @see https://docs.vendoservices.com/docs/standard-join-link#redirection-parameters
 * @method string getSuccessUrl()
 * @method setDeclineUrl(string $url) The user will be redirected to this URL if the transaction failed
 * @method string getDeclineUrl()
 * @method setLogindataHide(int $hide) Set to 1 to hide the login details in the confirmation page
 * @method string getLogindataHide()
 * @method setRef(string $merchantReference) This is a reference (a.k.a. merchant reference) that will be posted back to you on every postback
 * @method string getRef()
 * @method setAffiliateId(int $affiliateId) We recommend to pass in the Affiliate ID so Vendo can track risk and also provide reporting on this value. Set 0 for organic/search engine referral.
 * @method string getAffiliateId()
 * @method setCampaignId(int $campaignId)
 * @method int getCampaignId()
 * @method setProgramId(int $programId)
 * @method int getProgramId()
 * @method setSiteName(string $siteName) Set this only when working with whitelabed sites.
 * @method string getSiteName()
 * @method setSiteUrl(string $siteUrl) Set this only when working with whitelabed sites.
 * @method string getSiteUrl()
 */
class Join extends Base
{
    public function getBaseUrl(): string
    {
        return parent::getBaseUrl() . '/v/signup';
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
        if ($paramName === 'offers' || $paramName === 'xsales' || $paramName === 'xsale_ref') {
            if (is_array($paramValue)) {
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
            if (strlen($value) !== 'join') {
                throw new Exception('join is the only accepted value for the page parameter');
            }
        };
        $this->urlParamValidators['site_url'] = $this->urlParamValidators['success_url'];
    }

    protected function setAllowedUrlParameters(): void
    {
        $this->allowedUrlParams = [
            //standard parameters
            'site',
            'page',
            'country',
            'language',
            //'signature', //this parameter will be autogenerated when calling $this->getSignedUrl()
            //join process parameters
            'email',
            'username',
            'password',
            'password_encrypted',
            'email_hide',
            'username_hide',
            'password_hide',
            'subscription',
            //One step join process parameters
            'firstname',
            'lastname',
            'street',
            'city',
            'zip',
            'state',
            //Offers parameters
            'offers',
            'selected_offer',
            //Cross sales parameters
            'xsales_max',
            'xsales',
            'xsale_ref',
            //Redirection parameters
            'decline_url',
            'success_url',
            //Confirmation page parameters
            'logindata_hide',
            //User Management API Parameters
            'ref',
            //Affiliate tracking parameters
            'affiliate_id',
            'campaign_id',
            'program_id',
            //Parameters for white-labelled sites
            'site_name',
            'site_url',
            //signed url expiration
            'expires',
        ];
    }
}
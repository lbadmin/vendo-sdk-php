<?php
namespace VendoSdk\Url;

use VendoSdk\Exception;
use VendoSdk\Util\SignatureTrait;
use VendoSdk\Vendo;

abstract class Base
{
    use SignatureTrait;

    protected $allowedUrlParams = [];
    protected $urlParamValues = [];
    protected $urlParamValidators = [];

    public function __construct(string $sharedSecret) {
        $this->setAllowedUrlParameters();
        $this->setUrlParametersValidators();
        $this->setSharedSecret($sharedSecret);
    }

    protected abstract function setAllowedUrlParameters(): void;

    public function getBaseUrl(): string {
        return getenv("VENDO_BASE_URL", true)?:Vendo::BASE_URL;
    }

    protected function setUrlParametersValidators(): void {
        $this->urlParamValidators['email'] = function ($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('This email address is not valid ' . $email);
            }
        };
        $this->urlParamValidators['success_url'] = function ($url) {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception('This email address is not valid ' . $url);
            }
        };

        $this->urlParamValidators['decline_url'] = $this->urlParamValidators['success_url'];
    }

    /**
     * @param string $paramName
     * @param mixed $paramValue
     * @throws Exception
     */
    public function __set(string $paramName, $paramValue): void {
        if (!in_array($paramName, $this->allowedUrlParams)) {
            throw new Exception('Url parameter "' . $paramName . '" is not valid for this operation');
        }
        if (isset($this->urlParamValidators[$paramName]) && is_callable($this->urlParamValidators[$paramName])) {
            $this->urlParamValidators[$paramName]($paramValue);
        }
        $this->urlParamValues[$paramName] = $paramValue;
    }

    /**
     * @param string $paramName
     * @return mixed|null
     * @throws Exception
     */
    public function __get(string $paramName) {
        if (!in_array($paramName, $this->allowedUrlParams)) {
            throw new Exception('Url parameter ' . $paramName . ' is not valid for this operation');
        }
        return $this->urlParamValues[$paramName] ?? null;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {

        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        } else {
            $operation = substr($name, 0, 3);

            if ($operation === 'set' ||  $operation === 'get') {
                $paramName = substr($name, 3);
                //CamelCase to snake_case conversion. It will convert SiteName to site_name
                $paramName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $paramName));
                if ($operation === 'set') {
                    $this->{$paramName} = $arguments[0];
                } else {
                    return $this->{$paramName};
                }
            }
        }
    }

    public function getUrl(): string {
        $this->urlParamValues['sdkv'] = Vendo::SDK_VERSION;
        return $this->getBaseUrl() . '?' . http_build_query($this->urlParamValues);
    }

    public function getSignedUrl(): string {
        return $this->signUrl($this->getUrl());
    }

}
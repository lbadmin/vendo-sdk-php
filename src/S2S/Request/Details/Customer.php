<?php

namespace VendoSdk\S2S\Request\Details;

use VendoSdk\Exception;

class Customer extends ShippingAddress implements \JsonSerializable
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $language;

    /** @var string */
    protected $nationalIdentifier;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @throws Exception
     */
    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('This email address is not valid -> ' . $email);
        }
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->language;
    }

    /**
     * You must pass a valid ISO-639-1 string. e.g. en, es, fr, de, nl, pt, pl
     *
     * @param string $languageCode
     * @throws Exception
     */
    public function setLanguageCode(string $languageCode): void
    {
        if (strlen($languageCode) != 2) {
            throw new Exception('The language code must be a 2 letter string');
        }
        $this->language = strtolower($languageCode);
    }

    /**
     * @return string
     */
    public function getNationalIdentifier(): string
    {
        return $this->nationalIdentifier;
    }

    /**
     * @param string $nationalIdentifier
     */
    public function setNationalIdentifier(string $nationalIdentifier): void
    {
        $this->nationalIdentifier = $nationalIdentifier;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        if (empty($this->firstName)) {
            throw new Exception('You must set the firstName field in ' . get_class($this));
        }
        if (empty($this->lastName)) {
            throw new Exception('You must set the lastName field in ' . get_class($this));
        }
        if (empty($this->email)) {
            throw new Exception('You must set the email field in ' . get_class($this));
        }
        if (empty($this->language)) {
            throw new Exception('You must set the language field in ' . get_class($this));
        }
        if (empty($this->country)) {
            throw new Exception('You must set the country field in ' . get_class($this));
        }

        $result = array_filter([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'language' => $this->language,
            'email' => $this->email,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postalCode,
            'phone' => $this->phone,
        ]);

        if (!empty($this->nationalIdentifier)) {
            $result['national_identifier'] = $this->nationalIdentifier;
        }

        return $result;
    }
}

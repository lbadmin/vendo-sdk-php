<?php
namespace VendoSdk\S2S\Request\Details;

use VendoSdk\Exception;

class ShippingAddress implements \JsonSerializable
{
    /** @var string */
    protected $firstName;
    /** @var string */
    protected $lastName;
    /** @var ?string */
    protected $address;
    /** @var ?string */
    protected $city;
    /** @var ?string */
    protected $state;
    /** @var string */
    protected $country;
    /** @var ?string */
    protected $postalCode;
    /** @var ?string */
    protected $phone;

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }


    /**
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param ?string $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param ?string $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return ?string
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param ?string $state
     */
    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->country;
    }

    /**
     * You must pass a valid ISO 3166-1 alpha-2 Country Code string e.g. US, CA, DE, GB, ES, PY, PL, DK
     *
     * @param string $countryCode
     * @throws Exception
     */
    public function setCountryCode(string $countryCode): void
    {
        if (strlen($countryCode) != 2) {
            throw new Exception('The country code must be a 2 letter string');
        }
        $this->country = strtoupper($countryCode);
    }

    /**
     * @return ?string
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param ?string $postalCode
     */
    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param ?string $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
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
        if (empty($this->address)) {
            throw new Exception('You must set the address field in ' . get_class($this));
        }
        if (empty($this->city)) {
            throw new Exception('You must set the city field in ' . get_class($this));
        }
        if (empty($this->state)) {
            throw new Exception('You must set the state field in ' . get_class($this));
        }
        if (empty($this->postalCode)) {
            throw new Exception('You must set the postalCode field in ' . get_class($this));
        }
        if (empty($this->country)) {
            throw new Exception('You must set the country field in ' . get_class($this));
        }
        if (empty($this->phone)) {
            throw new Exception('You must set the phone field in ' . get_class($this));
        }

        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postalCode,
            'phone' => $this->phone,
        ];
    }
}

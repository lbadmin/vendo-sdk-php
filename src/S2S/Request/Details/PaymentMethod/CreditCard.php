<?php
namespace VendoSdk\S2S\Request\Details\PaymentMethod;

use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\PaymentDetails;

class CreditCard implements PaymentDetails, \JsonSerializable
{

    /** @var string */
    protected $cardNumber;
    /** @var string */
    protected $expirationMonth;
    /** @var string */
    protected $expirationYear;
    /** @var string */
    protected $cvv;
    /** @var string */
    protected $nameOnCard;

    /**
     * @return string
     */
    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    /**
     * @param string $cardNumber
     */
    public function setCardNumber(string $cardNumber): void
    {
        $this->cardNumber = $cardNumber;
    }

    /**
     * @return string
     */
    public function getExpirationMonth(): string
    {
        return $this->expirationMonth;
    }

    /**
     * Set the expiration month using two digits. Examples: 01 for January, 02 for February, 10 for October, etc.
     *
     * @param string $expirationMonth
     */
    public function setExpirationMonth(string $expirationMonth): void
    {
        $expirationMonth = \substr('0' . $expirationMonth, -2);
        $this->expirationMonth = $expirationMonth;
    }

    /**
     * @return string
     */
    public function getExpirationYear(): string
    {
        return $this->expirationYear;
    }

    /**
     * @param string $expirationYear
     */
    public function setExpirationYear(string $expirationYear): void
    {
        $this->expirationYear = $expirationYear;
    }

    /**
     * @return string
     */
    public function getCvv(): string
    {
        return $this->cvv;
    }

    /**
     * @param string $cvv
     */
    public function setCvv(string $cvv): void
    {
        $this->cvv = $cvv;
    }

    /**
     * @return string
     */
    public function getNameOnCard(): string
    {
        return $this->nameOnCard;
    }

    /**
     * @param string $nameOnCard
     */
    public function setNameOnCard(string $nameOnCard): void
    {
        $this->nameOnCard = $nameOnCard;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        if (empty($this->cardNumber)) {
            throw new Exception('You must set the cardNumber field in ' . get_class($this));
        }
        if (empty($this->nameOnCard)) {
            throw new Exception('You must set the nameOnCard field in ' . get_class($this));
        }
        if (empty($this->expirationMonth)) {
            throw new Exception('You must set the expirationMonth field in ' . get_class($this));
        }
        if (empty($this->expirationYear)) {
            throw new Exception('You must set the expirationYear field in ' . get_class($this));
        }

        $this->validateExpirationDate();

        return array_filter([
            'payment_method' => 'card',
            'card_number' => $this->cardNumber,
            'expiration_month' => $this->expirationMonth,
            'expiration_year' => $this->expirationYear,
            'cvv' => $this->cvv,
            'name_on_card' => $this->nameOnCard,
        ]);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    protected function validateExpirationDate(): void
    {
        $expirationDate = $this->expirationYear . '-' . $this->expirationMonth . '-01 23:59:59';
        $expirationTime = (new \DateTime($expirationDate))->modify('last day of')->getTimestamp();

        if ($expirationTime < time()) {
            throw new Exception('You entered an invalid expiration month and year. The credit card expired.');
        }
    }
}

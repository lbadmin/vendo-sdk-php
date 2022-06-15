<?php
namespace VendoSdk\Gateway;

use VendoSdk\Exception;

class ChangeSubscription extends SubscriptionBase implements \JsonSerializable
{
    /**
     * @inheritdoc
     */
    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/payment';
    }

    /**
     * @return CreditCard
     */
    public function getCreditCardDetails(): CreditCard
    {
        return $this->creditCardDetails;
    }

    /**
     * @param CreditCard $creditCardDetails
     */
    public function setCreditCardDetails(CreditCard $creditCardDetails): void
    {
        $this->creditCardDetails = $creditCardDetails;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        $fields = $this->getBaseFields();

        return $fields;
    }
}

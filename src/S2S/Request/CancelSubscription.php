<?php
namespace VendoSdk\S2S\Request;

use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\SubscriptionSchedule;

class CancelSubscription extends SubscriptionBase implements \JsonSerializable
{
    /** @var int */
    protected $reasonId;

    /**
     * @inheritdoc
     */
    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/cancel-subscription';
    }

    /**
     * @return ?int
     */
    public function getReasonId(): ?int
    {
        return $this->reasonId;
    }

    /**
     * @param int $reasonId
     */
    public function setReasonId(int $reasonId): void
    {
        $this->reasonId = $reasonId;
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

    public function getBaseFields(): array
    {
        $result = parent::getBaseFields();

        if(!empty($this->getReasonId())){
            $result['reason_id'] = $this->getReasonId();
        }

        return $result;
    }
}

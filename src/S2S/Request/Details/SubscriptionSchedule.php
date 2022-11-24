<?php
namespace VendoSdk\S2S\Request\Details;

use VendoSdk\Exception;

class SubscriptionSchedule implements \JsonSerializable
{
    /** @var string -- Y-m-d, future date */
    protected $nextRebillDate;

    /** @var float -- 10.34 */
    protected $rebillAmount;

    /** @var int -- 15, in days */
    protected $rebillDuration;

    /**
     * @return string
     */
    public function getNextRebillDate(): string
    {
        return $this->nextRebillDate;
    }

    /**
     * @param string $nextRebillDate
     */
    public function setNextRebillDate(string $nextRebillDate): void
    {
        $this->nextRebillDate = $nextRebillDate;
    }

    /**
     * @return float
     */
    public function getRebillAmount(): float
    {
        return $this->rebillAmount;
    }

    /**
     * @param float $rebillAmount
     */
    public function setRebillAmount(float $rebillAmount): void
    {
        $this->rebillAmount = $rebillAmount;
    }

    /**
     * @return int
     */
    public function getRebillDuration(): int
    {
        return $this->rebillDuration;
    }

    /**
     * @param int $rebillDuration
     */
    public function setRebillDuration(int $rebillDuration): void
    {
        $this->rebillDuration = $rebillDuration;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function jsonSerialize()
    {
        if(!empty($this->nextRebillDate)){
            $result['next_rebill_date'] = $this->nextRebillDate;
        }

        if(!empty($this->rebillAmount)){
            $result['rebill_amount'] = $this->rebillAmount;
        }

        if(!empty($this->rebillDuration)){
            $result['rebill_duration'] = $this->rebillDuration;
        }

        return $result ?? [];
    }
}

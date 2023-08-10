<?php
namespace VendoSdk\S2S\Request\Details;

use VendoSdk\Exception;

/**
 * Pass through values
 */
class ExternalReferences implements \JsonSerializable
{
    /** @var string */
    protected $transactionReference;
    /** @var ?string */
    protected $programId;
    /** @var ?string */
    protected $campaignId;
    /** @var ?string */
    protected $affiliateId;
    /** @var ?int */
    protected $initialTransactionId;

    /**
     * @return string
     */
    public function getTransactionReference(): string
    {
        return $this->transactionReference;
    }

    /**
     * @param string $transactionReference
     */
    public function setTransactionReference(string $transactionReference): void
    {
        $this->transactionReference = $transactionReference;
    }

    /**
     * @return string|null
     */
    public function getProgramId(): ?string
    {
        return $this->programId;
    }

    /**
     * @param string|null $programId
     */
    public function setProgramId(?string $programId): void
    {
        $this->programId = $programId;
    }

    /**
     * @return string|null
     */
    public function getCampaignId(): ?string
    {
        return $this->campaignId;
    }

    /**
     * @param string|null $campaignId
     */
    public function setCampaignId(?string $campaignId): void
    {
        $this->campaignId = $campaignId;
    }

    /**
     * @return string|null
     */
    public function getAffiliateId(): ?string
    {
        return $this->affiliateId;
    }

    /**
     * @param string|null $affiliateId
     */
    public function setAffiliateId(?string $affiliateId): void
    {
        $this->affiliateId = $affiliateId;
    }

    /**
     * @return int|null
     */
    public function getInitialTransactionId(): ?int
    {
        return $this->initialTransactionId;
    }

    /**
     * @param int|null $initialTransactionId
     */
    public function setInitialTransactionId(?string $initialTransactionId): void
    {
        $this->initialTransactionId = $initialTransactionId;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        if (empty($this->transactionReference)) {
            throw new Exception('You must set the transactionReference field in ' . get_class($this));
        }
        return array_filter([
            'transaction_reference' => $this->transactionReference,
            'program_id' => $this->programId,
            'campaign_id' => $this->campaignId,
            'affiliate_id' => $this->affiliateId,
        ]);

    }
}
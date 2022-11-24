<?php
namespace VendoSdk\S2S\Response\Details;

class SepaPaymentResult
{
    /** @var ?string */
    protected $mandateId;
    /** @var ?\DateTime */
    protected $mandateSignedDate;

    /**
     * @param array $sepaDetails
     * @throws \Exception
     */
    public function __construct(array $sepaDetails)
    {
        $this->setMandateId($sepaDetails['mandate_id'] ?? null);
        $this->setMandateSignedDate($sepaDetails['mandate_signed_date'] ?? null);
    }

    /**
     * @return string|null
     */
    public function getMandateId(): string
    {
        return $this->mandateId;
    }

    /**
     * @param string $mandateId|null
     */
    public function setMandateId(?string $mandateId): void
    {
        $this->mandateId = $mandateId;
    }

    /**
     * @return \DateTime|null
     */
    public function getMandateSignedDate(): ?\DateTime
    {
        return $this->mandateSignedDate;
    }

    /**
     * @param string|null $mandateSignedDate
     * @throws \Exception
     */
    public function setMandateSignedDate(?string $mandateSignedDate): void
    {
        $dt = null;
        if (!empty($mandateSignedDate)) {
            $dt = new \DateTime($mandateSignedDate);
        }
        $this->mandateSignedDate = $dt;
    }
}

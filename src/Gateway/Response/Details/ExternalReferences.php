<?php
namespace VendoSdk\Gateway\Response\Details;

class ExternalReferences extends \VendoSdk\Gateway\Request\Details\ExternalReferences
{
    /**
     * @param array $extRef
     */
    public function __construct(array $extRef)
    {
        $this->setAffiliateId($extRef['affiliate_id'] ?? null);
        $this->setCampaignId($extRef['campaign_id'] ?? null);
        $this->setProgramId($extRef['program_id'] ?? null);
        $this->setTransactionReference($extRef['transaction_reference'] ?? 'null');
    }
}

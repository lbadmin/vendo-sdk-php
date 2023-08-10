<?php

namespace VendoSdk\S2S\Request\Details\PaymentMethod;

use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\PaymentDetails;

class Verification implements PaymentDetails, \JsonSerializable
{
    /** @var int */
    protected $verificationId;

    /**
     * @return int
     */
    public function getVerificationId(): int
    {
        return $this->verificationId;
    }

    /**
     * @param int $verificationId
     */
    public function setVerificationId(int $verificationId): void
    {
        $this->verificationId = $verificationId;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        if (empty($this->verificationId)) {
            throw new Exception('You must set the verificationId field in ' . get_class($this));
        }

        return [
            'verification_id' => $this->verificationId,
        ];
    }
}

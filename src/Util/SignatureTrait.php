<?php
namespace VendoSdk\Util;

trait SignatureTrait
{
    protected $sharedSecret;

    public function setSharedSecret(string $sharedSecret): void {
        $this->sharedSecret = $sharedSecret;
    }
    public function getSharedSecret(): string {
        return $this->sharedSecret;
    }

    public function signUrl(string $url): string {
        $signer = new Signature($this->getSharedSecret());
        return $signer->sign($url);
    }
}
<?php
namespace VendoSdk\S2S\Request\Details;

use VendoSdk\Exception;

class ClientRequest implements \JsonSerializable
{
    /** @var string */
    protected $ipAddress;

    /** @var ?string */
    protected $browserUserAgent;

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return ?string
     */
    public function getBrowserUserAgent(): ?string
    {
        return $this->browserUserAgent;
    }

    /**
     * @param ?string $browserUserAgent
     */
    public function setBrowserUserAgent(?string $browserUserAgent): void
    {
        $this->browserUserAgent = $browserUserAgent;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        if (empty($this->ipAddress)) {
            throw new Exception('You must set the ipAddress field in ' . get_class($this));
        }

        return array_filter([
            'ip_address' => $this->getIpAddress(),
            'browser_user_agent' => $this->getBrowserUserAgent(),
        ]);
    }
}

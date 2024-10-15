<?php

namespace VendoSdk\S2S\Response\Details;

class OxxoPaymentResult
{
    protected $barcode;

    protected $expiresAt;

    protected $pdfGzipBase64;

    protected $pngGzipBase64;

    public function __construct(array $oxxoDetails)
    {
        $this->setBarcode($oxxoDetails['oxxo_barcode'] ?? null);
        $this->setExpiresAt($oxxoDetails['expiration_date'] ?? null);
        $this->setPdfGzipBase64($oxxoDetails['oxxo_pdf_gzip_base_64'] ?? null);
        $this->setPngGzipBase64($oxxoDetails['oxxo_barcode_png_gzip_base_64'] ?? null);
    }

    /**
     * @return mixed
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param mixed $barcode
     */
    public function setBarcode($barcode): void
    {
        $this->barcode = $barcode;
    }

    /**
     * @return mixed
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param mixed $expiresAt
     */
    public function setExpiresAt($expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return mixed
     */
    public function getPdfGzipBase64()
    {
        return $this->pdfGzipBase64;
    }

    /**
     * @param mixed $pdfGzipBase64
     */
    public function setPdfGzipBase64($pdfGzipBase64): void
    {
        $this->pdfGzipBase64 = $pdfGzipBase64;
    }

    /**
     * @return mixed
     */
    public function getPngGzipBase64()
    {
        return $this->pngGzipBase64;
    }

    /**
     * @param mixed $pngGzipBase64
     */
    public function setPngGzipBase64($pngGzipBase64): void
    {
        $this->pngGzipBase64 = $pngGzipBase64;
    }
}

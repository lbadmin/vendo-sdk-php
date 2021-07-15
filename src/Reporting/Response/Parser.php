<?php
namespace VendoSdk\Reporting\Response;

/**
 * This extends php's \SimpleXMLElement and type hints all the properties of the response
 * returned by Vendo's Reconciliation API
 *
 * @package VendoSdk\Util
 *
 * @property $error
 * @property HeaderElement $header
 * @property RowElement[] $body
 */
class Parser extends \SimpleXMLElement
{
}
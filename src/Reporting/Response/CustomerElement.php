<?php
namespace VendoSdk\Reporting\Response;

/**
 * You can get the Vendo Customer ID with $customerElement['id']
 *
 * @package VendoSdk\Reporting\Response
 *
 * @property string $email
 * @property string $firstname
 * @property string $lastname
 * @property string $street
 * @property string $zip
 * @property string $city
 * @property string $country
 * @property string $language
 * @property string $ip
 */
abstract class CustomerElement implements \ArrayAccess
{}
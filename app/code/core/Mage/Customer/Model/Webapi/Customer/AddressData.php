<?php
/**
 * Data structure description for customer address entity
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Customer_Model_Webapi_Customer_AddressData
{
    /**
     * Street
     *
     * @var string
     */
    public $street;

    /**
     * City
     *
     * @var string
     */
    public $city;

    /**
     * State
     *
     * @optional true
     * @var string
     */
    public $state;
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Sales
 */
class Mage_Sales_Utility_Address
{
    public static function getAddress($type)
    {
        $address = new Mage_Sales_Model_Order_Address;
        $address->setRegion('CA')
            ->setPostcode('11111')
            ->setFirstname('firstname')
            ->setLastname('lastname')
            ->setStreet('street')
            ->setCity('Los Angeles')
            ->setEmail('admin@example.com')
            ->setTelephone('1111111111')
            ->setCountryId('US')
            ->setAddressType($type);
        return $address;
    }
}

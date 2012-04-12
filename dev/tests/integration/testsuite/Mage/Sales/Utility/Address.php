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
    /**
     * Data for address objects
     *
     * @var array
     */
    protected static $_data = array(
        'region'     => 'CA',
        'postcode'   => '11111',
        'lastname'   => 'lastname',
        'street'     => 'street',
        'city'       => 'Los Angeles',
        'email'      => 'admin@example.com',
        'telephone'  => '11111111',
        'country_id' => 'US',
    );

    /**
     * Create Order Address instance and fill it with data
     *
     * @static
     * @param string $type
     * @return Mage_Sales_Model_Order_Address
     */
    public static function getOrderAddress($type)
    {
        $address = new Mage_Sales_Model_Order_Address(self::$_data);
        return $address->setAddressType($type);
    }

    /**
     * Create Quote Address instance and fill it with data
     *
     * @param string $type
     * @return Mage_Sales_Model_Quote_Address
     */
    public static function getQuoteAddress($type)
    {
        $address = new Mage_Sales_Model_Quote_Address(self::$_data);
        return $address->setAddressType($type);
    }

    /**
     * Get address data
     *
     * @static
     * @return array
     */
    public static function getAddressData()
    {
        return self::$_data;
    }
}

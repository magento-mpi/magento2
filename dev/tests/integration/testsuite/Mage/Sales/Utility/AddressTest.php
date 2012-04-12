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
class Mage_Sales_Utility_AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $type
     * @dataProvider getAddressDataProvider
     */
    public function testGetOrderAddress($type)
    {
        $address = Mage_Sales_Utility_Address::getOrderAddress($type);
        $this->assertInstanceOf('Mage_Sales_Model_Order_Address', $address);
        $this->assertEquals($type, $address->getAddressType());
    }

    /**
     * @param string $type
     * @dataProvider getAddressDataProvider
     */
    public function testGetQuoteAddress($type)
    {
        $address = Mage_Sales_Utility_Address::getQuoteAddress($type);
        $this->assertInstanceOf('Mage_Sales_Model_Quote_Address', $address);
        $this->assertEquals($type, $address->getAddressType());
    }

    public function getAddressDataProvider()
    {
        return array(
            array('billing'),
            array('shipping'),
        );
    }

    public function testGetAddressData()
    {
        $this->assertInternalType('array', Mage_Sales_Utility_Address::getAddressData());
    }
}

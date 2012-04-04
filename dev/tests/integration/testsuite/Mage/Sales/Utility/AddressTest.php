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
    public function testGetAddress($type)
    {
        $address = Mage_Sales_Utility_Address::getAddress($type);
        $this->assertInstanceOf('Mage_Sales_Model_Order_Address', $address);
        $this->assertEquals($type, $address->getAddressType());
    }

    public function getAddressDataProvider()
    {
        return array(
            array('billing'),
            array('shipping'),
        );
    }
}
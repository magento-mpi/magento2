<?php
/**
 * Config helper Unit tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Class implements tests for Mage_Webapi_Helper_Config class.
 */
class Mage_Webapi_Helper_ConfigTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Helper_Config */
    protected $_helper;

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $mockContext = $this->getMockBuilder('Mage_Core_Helper_Context')->disableOriginalConstructor()->getMock();
        $this->_helper = new Mage_Webapi_Helper_Config($mockContext);
        parent::setUp();
    }

    /**
     * Test identifying resource name parts including subresources using class name.
     *
     * @dataProvider resourceNamePartsDataProvider
     */
    public function testGetResourceNameParts($className, $preserveVersion, $expected)
    {
        $actual = $this->_helper->getServiceNameParts(
            $className,
            $preserveVersion
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * Dataprovider for resourceNameParts
     *
     * @return array
     */
    public function resourceNamePartsDataProvider()
    {
        return array(
            array('Mage_Customer_Service_Customer_AddressInterfaceV1', false, array('Customer', 'Address')),
            array(
                'Vendor_Customer_Service_Customer_AddressInterfaceV1',
                true,
                array('VendorCustomer', 'Address', 'V1')
            ),
            array('Mage_Catalog_Service_ProductInterfaceV2', true, array('CatalogProduct', 'V2'))
        );
    }

}

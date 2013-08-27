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
 * Class implements tests for Mage_Webapi_Helper_Data class.
 */
class Mage_Webapi_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $mockContext = $this->getMockBuilder('Mage_Core_Helper_Context')->disableOriginalConstructor()->getMock();
        $this->_helper = new Mage_Webapi_Helper_Data($mockContext);
        parent::setUp();
    }

    /**
     * Test identifying service name parts including subservices using class name.
     *
     * @dataProvider serviceNamePartsDataProvider
     */
    public function testGetServiceNameParts($className, $preserveVersion, $expected)
    {
        $actual = $this->_helper->getServiceNameParts(
            $className,
            $preserveVersion
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * Dataprovider for serviceNameParts
     *
     * @return array
     */
    public function serviceNamePartsDataProvider()
    {
        return array(
            array('Mage_Customer_Service_Customer_AddressV1Interface', false, array('Customer', 'Address')),
            array(
                'Vendor_Customer_Service_Customer_AddressV1Interface',
                true,
                array('VendorCustomer', 'Address', 'V1')
            ),
            array('Mage_Catalog_Service_ProductV2Interface', true, array('CatalogProduct', 'V2'))
        );
    }

}

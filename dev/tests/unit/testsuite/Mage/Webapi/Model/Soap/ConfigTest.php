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
 * Class implements tests for Mage_Webapi_Model_Soap_Config class.
 */
class Mage_Webapi_Model_Soap_ConfigTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Model_Soap_Config */
    protected $_soapConfig;

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $objectManagerMock = $this->getMockBuilder('Mage_Core_Model_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $fileSystemMock = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $dirMock = $this->getMockBuilder('Mage_Core_Model_Dir')->disableOriginalConstructor()->getMock();
        $configMock = $this->getMockBuilder('Mage_Webapi_Model_Config')->disableOriginalConstructor()->getMock();
        $helperMock = $this->getMockBuilder('Mage_Core_Helper_Data')->disableOriginalConstructor()->getMock();
        $this->_soapConfig = new Mage_Webapi_Model_Soap_Config(
            $objectManagerMock,
            $fileSystemMock,
            $dirMock,
            $configMock,
            $helperMock
        );
        parent::setUp();
    }

    /**
     * Test identifying service name parts including subservices using class name.
     *
     * @dataProvider serviceNamePartsDataProvider
     */
    public function testGetServiceNameParts($className, $preserveVersion, $expected)
    {
        $actual = $this->_soapConfig->getServiceNameParts(
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

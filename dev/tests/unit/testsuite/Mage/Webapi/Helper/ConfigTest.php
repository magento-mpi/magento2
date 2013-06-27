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
     * @dataProvider resourceNamePartsDataProvider
     */
    public function testGetResourceNameParts($className, $moduleNamespace, $moduleName, $version)
    {
        if ($version == null) {
            $expected = array($moduleNamespace, $moduleName);
            $preserveVersion = false;
        } else {
            $expected = array($moduleNamespace, $moduleName, $version);
            $preserveVersion = true;
        }
        $actual = $this->_helper->getResourceNameParts(
            $className,
            $preserveVersion
        );
        $this->assertEquals($expected, $actual);
    }

    public function resourceNamePartsDataProvider()
    {
        return array(
            array('Mage_Customer_Service_Customer_AddressInterfaceV1', 'Customer', 'Address', null),
            array('Vendor_Customer_Service_Customer_AddressInterfaceV1', 'VendorCustomer', 'Address', 'V1'),
            array('Mage_Catalog_Service_ProductInterfaceV2', 'Catalog', 'Product', 'V2')
        );
    }

}

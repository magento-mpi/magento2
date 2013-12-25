<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Helper;

/**
 * Class implements tests for \Magento\Webapi\Helper\Config class.
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Helper\Config */
    protected $_configHelper;

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $helperContext = $this->getMock('Magento\App\Helper\Context', [], [], '', false);
        $this->_configHelper = new \Magento\Webapi\Helper\Config($helperContext);
        parent::setUp();
    }

    /**
     * Test identifying service name parts including subservices using class name.
     *
     * @dataProvider serviceNamePartsDataProvider
     */
    public function testGetServiceNameParts($className, $preserveVersion, $expected)
    {
        $actual = $this->_configHelper->getServiceNameParts(
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
            array('Magento\Customer\Service\Customer\AddressV1Interface', false, array('Customer', 'Address')),
            array(
                'Vendor\Customer\Service\Customer\AddressV1Interface',
                true,
                array('VendorCustomer', 'Address', 'V1')
            ),
            array('Magento\Catalog\Service\ProductV2Interface', true, array('CatalogProduct', 'V2'))
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider dataProviderForTestGetServiceNamePartsInvalidName
     */
    public function testGetServiceNamePartsInvalidName($interfaceClassName)
    {
        $this->_configHelper->getServiceNameParts($interfaceClassName);
    }

    public function dataProviderForTestGetServiceNamePartsInvalidName()
    {
        return array(
            array('BarV1Interface'), // Missed vendor, module, 'Service'
            array('Service\\V1Interface'), // Missed vendor and module
            array('Magento\\Foo\\Service\\BarVxInterface'), // Version number should be a number
            array('Magento\\Foo\\Service\\BarInterface'), // Version missed
            array('Magento\\Foo\\Service\\BarV1'), // 'Interface' missed
            array('Foo\\Service\\BarV1Interface'), // Module missed
            array('Foo\\BarV1Interface'), // Module and 'Service' missed
        );
    }
}

require_once '/../_files/test_interfaces.php';


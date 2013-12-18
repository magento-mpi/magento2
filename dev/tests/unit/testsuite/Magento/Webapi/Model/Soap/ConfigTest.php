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
 * Class implements tests for \Magento\Webapi\Model\Soap\Config class.
 */
namespace Magento\Webapi\Model\Soap;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Model\Soap\Config */
    protected $_soapConfig;

    /** @var \Magento\Webapi\Model\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $_configMock;

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $objectManagerMock = $this->getMockBuilder('Magento\App\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $fileSystemMock = $this->getMockBuilder('Magento\Filesystem')->disableOriginalConstructor()->getMock();
        $this->_configMock = $this->getMockBuilder('Magento\Webapi\Model\Config')
            ->disableOriginalConstructor()->getMock();
        $this->_soapConfig = new \Magento\Webapi\Model\Soap\Config(
            $objectManagerMock,
            $fileSystemMock,
            $this->_configMock
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
        $this->_soapConfig->getServiceNameParts($interfaceClassName);
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

    public function testGetRequestedSoapServices()
    {
        $servicesConfig = array(
            'Magento\Module\Service\FooV1Interface' => array(
                'class' => 'Magento\Module\Service\FooV1Interface',
                'baseUrl' => '/V1/foo',
                'methods' => array(
                    'someMethod' => array(
                        'httpMethod' => 'GET',
                        'method' => 'someMethod',
                        'route' => '',
                        'isSecure' => false,
                        'resources' => array('Magento_TestModule1::resource1')
                    )
                )
            ),
            'Magento\Module\Service\BarV1Interface' => array(
                'class' => 'Magento\Module\Service\BarV1Interface',
                'baseUrl' => '/V1/bar',
                'methods' => array(
                    'someMethod' => array(
                        'httpMethod' => 'GET',
                        'method' => 'someMethod',
                        'route' => '',
                        'isSecure' => false,
                        'resources' => array('Magento_TestModule1::resource2')
                    )
                )
            )
        );

        $expectedResult = array(
            array(
                'methods' => array(
                    'someMethod' => array(
                        'method' => 'someMethod',
                        'inputRequired' => '',
                        'isSecure' => '',
                        'resources' => array('Magento_TestModule1::resource1')
                    )
                ),
                'class' => 'Magento\Module\Service\FooV1Interface'
            )
        );

        $this->_configMock->expects($this->once())->method('getServices')->will($this->returnValue($servicesConfig));
        $result = $this->_soapConfig->getRequestedSoapServices(array('moduleFooV1', 'moduleBarV2', 'moduleBazV1'));
        $this->assertEquals($expectedResult, $result);
    }
}

namespace Magento\Module\Service;

interface FooV1Interface
{
    public function someMethod();
}

interface BarV1Interface
{
    public function someMethod();
}

interface FooBarV1Interface
{
    public function someMethod();
}

namespace Magento\Module\Service\Foo;

interface BarV1Interface
{
    public function someMethod();
}

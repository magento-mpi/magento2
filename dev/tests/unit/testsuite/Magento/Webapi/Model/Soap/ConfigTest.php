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

    /** @var \Magento\Webapi\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $_helperMock;

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $objectManagerMock = $this->getMockBuilder('Magento\App\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $fileSystemMock = $this->getMockBuilder('Magento\Filesystem')->disableOriginalConstructor()->getMock();
        $dirMock = $this->getMockBuilder('Magento\App\Dir')->disableOriginalConstructor()->getMock();
        $classReflection = $this->getMock(
            'Magento\Webapi\Model\Soap\Config\Reader\Soap\ClassReflector',
            ['reflectClassMethods'],
            [],
            '',
            false
        );
        $classReflection->expects($this->any())->method('reflectClassMethods')->will($this->returnValue(array()));
        $this->_helperMock = $this->getMock('Magento\Webapi\Helper\Data', [], [], '', false);
        $this->_configMock = $this->getMock('Magento\Webapi\Model\Config', [], [], '', false);
        $servicesConfig = array(
            'ModuleFooV1' => array(
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
            'ModuleBarV1' => array(
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
        $this->_configMock->expects($this->once())->method('getServices')->will($this->returnValue($servicesConfig));
        $this->_helperMock->expects($this->any())
            ->method('getServiceName')
            ->will(
                $this->returnValueMap(
                    array(
                        array('Magento\Module\Service\FooV1Interface', true, 'moduleFooV1'),
                        array('Magento\Module\Service\BarV1Interface', true, 'moduleBarV1')
                    )
                )
            );
        $this->_soapConfig = new \Magento\Webapi\Model\Soap\Config(
            $objectManagerMock,
            $fileSystemMock,
            $dirMock,
            $this->_configMock,
            $classReflection,
            $this->_helperMock
        );
        parent::setUp();
    }

    public function testGetRequestedSoapServices()
    {
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
        $result = $this->_soapConfig->getRequestedSoapServices(array('moduleFooV1', 'moduleBarV2', 'moduleBazV1'));
        $this->assertEquals($expectedResult, $result);
    }
}

require_once realpath(__DIR__ . '/../../_files/test_interfaces.php');

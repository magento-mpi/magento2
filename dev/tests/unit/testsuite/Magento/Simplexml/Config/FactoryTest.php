<?php
/**
 * Magento_Simplexml_Config_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Simplexml_Config_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Simplexml_Config_Factory */
    private $_configFactory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_mockObjectManager;

    public function setUp()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_configFactory = new Magento_Simplexml_Config_Factory($this->_mockObjectManager);
    }

    public function testCreate()
    {
        $xmlFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'data.xml';
        $sourceData = new Magento_Simplexml_Element(file_get_contents($xmlFile));
        $config = 'Config';
        $this->_mockObjectManager->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Magento_Simplexml_Config'),
                $this->equalTo(array('sourceData' => $sourceData))
            )
            ->will($this->returnValue($config));
        $this->assertSame($config, $this->_configFactory->create($sourceData));
    }

    public function testCreateNull()
    {
        $config = 'Config';
        $this->_mockObjectManager->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Magento_Simplexml_Config'),
                $this->equalTo(array('sourceData' => null))
            )
            ->will($this->returnValue($config));
        $this->assertSame($config, $this->_configFactory->create());
    }
}
<?php
/**
 * Magento_Widget_Model_Config_FileResolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Widget_Model_Config_FileResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Widget_Model_Config_FileResolver
     */
    private $_object;

    /** @var Magento_Core_Model_Dir/PHPUnit_Framework_MockObject_MockObject  */
    private $_applicationDirsMock;

    public function setUp()
    {
        $this->_applicationDirsMock = $this->getMockBuilder('Magento_Core_Model_Dir')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_object = $objectManager->create('Magento_Widget_Model_Config_FileResolver', array(
            'applicationDirs' => $this->_applicationDirsMock
        ));
    }

    public function testGetDesign()
    {
        $this->_applicationDirsMock->expects($this->any())
            ->method('getDir')
            ->will($this->returnValue(__DIR__ . '/_files/design'));
        $widgetConfigs = $this->_object->get('widget.xml', 'design');
        $expected = realpath(__DIR__ . '/_files/design/frontend/Test/etc/widget.xml');
        $this->assertCount(1, $widgetConfigs);
        $this->assertEquals($expected, realpath($widgetConfigs[0]));
    }
}
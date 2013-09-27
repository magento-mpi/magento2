<?php
/**
 * \Magento\Widget\Model\Config\FileResolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Model\Config;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Model\Config\FileResolver
     */
    private $_object;

    /** @var \Magento\Core\Model\Dir/PHPUnit_Framework_MockObject_MockObject  */
    private $_applicationDirsMock;

    public function setUp()
    {
        $this->_applicationDirsMock = $this->getMockBuilder('Magento\Core\Model\Dir')
            ->disableOriginalConstructor()
            ->getMock();

        $moduleListMock = $this->getMockBuilder('Magento\Core\Model\ModuleListInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $moduleListMock->expects($this->any())
            ->method('getModules')
            ->will($this->returnValue(array('Magento_Test' => array(
                'name' => 'Magento_Test',
                'version' => '1.11.1',
                'active' => 'true'
            ))));

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $moduleReader = $objectManager->create('Magento\Core\Model\Config\Modules\Reader', array(
            'moduleList' => $moduleListMock
        ));
        $moduleReader->setModuleDir('Magento_Test', 'etc', __DIR__ . '/_files/code/Magento/Test/etc');
        $this->_object = $objectManager->create('Magento\Widget\Model\Config\FileResolver', array(
            'moduleReader' => $moduleReader,
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

    public function testGetGlobal()
    {
        $this->_applicationDirsMock->expects($this->any())
            ->method('getDir')
            ->will($this->returnValue(__DIR__ . '/_files/code'));
        $widgetConfigs = $this->_object->get('widget.xml', 'global');
        $expected = realpath(__DIR__ . '/_files/code/Magento/Test/etc/widget.xml');
        $this->assertCount(1, $widgetConfigs);
        $this->assertEquals($expected, realpath($widgetConfigs[0]));
    }
}

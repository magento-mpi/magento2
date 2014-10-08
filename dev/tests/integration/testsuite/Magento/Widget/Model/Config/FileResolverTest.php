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

use Magento\Framework\App\Filesystem\DirectoryList;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Model\Config\FileResolver
     */
    private $_object;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\TestFramework\App\Filesystem $filesystem */
        $filesystem = $objectManager->get('Magento\Framework\App\Filesystem');
        $filesystem->overridePath(DirectoryList::MODULES, __DIR__ . '/_files/code');
        $filesystem->overridePath(DirectoryList::THEMES, __DIR__ . '/_files/design');
        $filesystem->overridePath(DirectoryList::CONFIG, __DIR__ . '/_files');

        $moduleListMock = $this->getMockBuilder(
            'Magento\Framework\Module\ModuleListInterface'
        )->disableOriginalConstructor()->getMock();
        $moduleListMock->expects(
            $this->any()
        )->method(
            'getModules'
        )->will(
            $this->returnValue(
                array('Magento_Test' => array('name' => 'Magento_Test', 'version' => '1.11.1', 'active' => 'true'))
            )
        );


        $moduleReader = $objectManager->create(
            'Magento\Framework\Module\Dir\Reader',
            array('moduleList' => $moduleListMock, 'filesystem' => $filesystem)
        );
        $moduleReader->setModuleDir('Magento_Test', 'etc', __DIR__ . '/_files/code/Magento/Test/etc');
        $this->_object = $objectManager->create(
            'Magento\Widget\Model\Config\FileResolver',
            array('moduleReader' => $moduleReader, 'filesystem' => $filesystem)
        );
    }

    public function testGetDesign()
    {
        $widgetConfigs = $this->_object->get('widget.xml', 'design');
        $expected = str_replace('\\', '/', realpath(__DIR__ . '/_files/design/frontend/Test/etc/widget.xml'));
        $actual = $widgetConfigs->key();
        $this->assertCount(1, $widgetConfigs);
        $this->assertStringEndsWith($actual, $expected);
    }

    public function testGetGlobal()
    {
        $widgetConfigs = $this->_object->get('widget.xml', 'global');
        $expected = str_replace('\\', '/', realpath(__DIR__ . '/_files/code/Magento/Test/etc/widget.xml'));
        $actual = $widgetConfigs->key();
        $this->assertCount(1, $widgetConfigs);
        $this->assertStringEndsWith($actual, $expected);
    }
}

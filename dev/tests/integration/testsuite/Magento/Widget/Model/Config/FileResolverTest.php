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

    /**
     * @var \Magento\Filesystem\DirectoryList
     */
    protected $directoryList;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Filesystem $filesystem */
        $filesystem = $objectManager->create(
            'Magento\Filesystem',
            array('directoryList' => $objectManager->create(
                    'Magento\Filesystem\DirectoryList',
                    array(
                        'root' => BP,
                        'directories' => array(
                            \Magento\Filesystem::MODULES => array('path' => __DIR__ . '/_files/code'),
                            \Magento\Filesystem::THEMES => array('path' => __DIR__ . '/_files/design'),
                            \Magento\Filesystem::CONFIG => array('path' => __DIR__ . '/_files/'),
                        )
                    )
                )
            )
        );

        $moduleListMock = $this->getMockBuilder('Magento\Module\ModuleListInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $moduleListMock->expects($this->any())
            ->method('getModules')
            ->will($this->returnValue(array('Magento_Test' => array(
                'name' => 'Magento_Test',
                'version' => '1.11.1',
                'active' => 'true'
            ))));


        $moduleReader = $objectManager->create('Magento\Module\Dir\Reader', array(
            'moduleList' => $moduleListMock,
            'filesystem' => $filesystem
        ));
        $moduleReader->setModuleDir('Magento_Test', 'etc', __DIR__ . '/_files/code/Magento/Test/etc');
        $this->_object = $objectManager->create('Magento\Widget\Model\Config\FileResolver', array(
            'moduleReader' => $moduleReader,
            'filesystem' => $filesystem
        ));

        $this->directoryList = $objectManager->get('Magento\Filesystem\DirectoryList');
        $dirPath = ltrim(str_replace($this->directoryList->getRoot(), '', str_replace('\\', '/', __DIR__))
            . '/_files', '/');
        $this->directoryList->addDirectory(\Magento\Filesystem::MODULES, array('path' => $dirPath));

    }

    public function testGetDesign()
    {
        $widgetConfigs  = $this->_object->get('widget.xml', 'design');
        $expected       = realpath(__DIR__ . '/_files/design/frontend/Test/etc/widget.xml');
        $actual         = $widgetConfigs->key();
        $this->assertCount(1, $widgetConfigs);
        $this->assertStringEndsWith($actual, $expected);
    }

    public function testGetGlobal()
    {
        $widgetConfigs  = $this->_object->get('widget.xml', 'global');
        $expected       = realpath(__DIR__ . '/_files/code/Magento/Test/etc/widget.xml');
        $actual         = $widgetConfigs->key();
        $this->assertCount(1, $widgetConfigs);
        $this->assertStringEndsWith($actual, $expected);
    }
}

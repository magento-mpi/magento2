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
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Framework\App\Filesystem $filesystem */
        $filesystem = $objectManager->create(
            'Magento\Framework\App\Filesystem',
            array(
                'directoryList' => $objectManager->create(
                    'Magento\Framework\App\Filesystem\DirectoryList',
                    array(
                        'root' => BP,
                        'directories' => array(
                            \Magento\Framework\App\Filesystem::MODULES_DIR => array('path' => __DIR__ . '/_files/code'),
                            \Magento\Framework\App\Filesystem::THEMES_DIR => array(
                                'path' => __DIR__ . '/_files/design'
                            ),
                            \Magento\Framework\App\Filesystem::CONFIG_DIR => array('path' => __DIR__ . '/_files/')
                        )
                    )
                )
            )
        );

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

        $this->directoryList = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
        $dirPath = ltrim(
            str_replace($this->directoryList->getRoot(), '', str_replace('\\', '/', __DIR__)) . '/_files',
            '/'
        );
        $this->directoryList->addDirectory(\Magento\Framework\App\Filesystem::MODULES_DIR, array('path' => $dirPath));
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

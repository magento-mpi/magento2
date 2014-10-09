<?php
/**
 * \Magento\Widget\Model\Config\Data
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
namespace Magento\Widget\Model\Config;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @magentoDataFixture Magento/Backend/controllers/_files/cache/all_types_disabled.php
 * @magentoAppArea adminhtml
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Model\Config\Data
     */
    protected $_configData;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\TestFramework\App\Filesystem $filesystem */
        $filesystem = $objectManager->get('Magento\Framework\App\Filesystem');
        $filesystem->overridePath(DirectoryList::MODULES, __DIR__ . '/_files/code');
        $filesystem->overridePath(DirectoryList::CONFIG, __DIR__ . '/_files/code');
        $filesystem->overridePath(DirectoryList::THEMES, __DIR__ . '/_files/design');

        /** @var \Magento\Framework\Module\Declaration\FileResolver $modulesDeclarations */
        $modulesDeclarations = $objectManager->create(
            'Magento\Framework\Module\Declaration\FileResolver',
            array(
                'filesystem' => $filesystem,
                'fileIteratorFactory' => $objectManager->create('Magento\Framework\Config\FileIteratorFactory')
            )
        );


        /** @var \Magento\Framework\Module\Declaration\Reader\Filesystem $filesystemReader */
        $filesystemReader = $objectManager->create(
            'Magento\Framework\Module\Declaration\Reader\Filesystem',
            array('fileResolver' => $modulesDeclarations)
        );

        /** @var \Magento\Framework\Module\ModuleList $modulesList */
        $modulesList = $objectManager->create(
            'Magento\Framework\Module\ModuleList',
            array('reader' => $filesystemReader)
        );

        /** @var \Magento\Framework\Module\Dir\Reader $moduleReader */
        $moduleReader = $objectManager->create(
            'Magento\Framework\Module\Dir\Reader',
            array('moduleList' => $modulesList, 'filesystem' => $filesystem)
        );
        $moduleReader->setModuleDir('Magento_Test', 'etc', __DIR__ . '/_files/code/Magento/Test/etc');

        /** @var \Magento\Widget\Model\Config\FileResolver $fileResolver */
        $fileResolver = $objectManager->create(
            'Magento\Widget\Model\Config\FileResolver',
            array('moduleReader' => $moduleReader, 'filesystem' => $filesystem)
        );

        $schema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget.xsd';
        $perFileSchema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget_file.xsd';
        $reader = $objectManager->create(
            'Magento\Widget\Model\Config\Reader',
            array(
                'moduleReader' => $moduleReader,
                'fileResolver' => $fileResolver,
                'schema' => $schema,
                'perFileSchema' => $perFileSchema
            )
        );

        $this->_configData = $objectManager->create('Magento\Widget\Model\Config\Data', array('reader' => $reader));
    }

    public function testGet()
    {
        $result = $this->_configData->get();
        $expected = include '_files/expectedGlobalDesignArray.php';
        $this->assertEquals($expected, $result);
    }
}

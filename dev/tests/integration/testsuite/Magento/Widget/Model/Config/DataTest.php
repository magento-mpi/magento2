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

/**
 * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_disabled.php
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
        /** @var \Magento\App\Dir $dirs */
        $dirs = $objectManager->create(
            'Magento\App\Dir', array(
                'baseDir' => BP,
                'dirs' => array(
                    \Magento\App\Dir::MODULES => __DIR__ . '/_files/code',
                    \Magento\App\Dir::CONFIG => __DIR__ . '/_files/code',
                    \Magento\App\Dir::THEMES => __DIR__ . '/_files/design',
                )
            )
        );

        /** @var \Magento\Core\Model\Module\Declaration\FileResolver $modulesDeclarations */
        $modulesDeclarations = $objectManager->create(
            'Magento\Core\Model\Module\Declaration\FileResolver', array(
                'applicationDirs' => $dirs,
            )
        );


        /** @var \Magento\Core\Model\Module\Declaration\Reader\Filesystem $filesystemReader */
        $filesystemReader = $objectManager->create(
            'Magento\Core\Model\Module\Declaration\Reader\Filesystem', array(
                'fileResolver' => $modulesDeclarations,
            )
        );

        /** @var \Magento\App\ModuleList $modulesList */
        $modulesList = $objectManager->create(
            'Magento\App\ModuleList', array(
                'reader' => $filesystemReader,
            )
        );

        /** @var \Magento\Core\Model\Config\Modules\Reader $moduleReader */
        $moduleReader = $objectManager->create(
            'Magento\Core\Model\Config\Modules\Reader', array(
                'moduleList' => $modulesList
            )
        );
        $moduleReader->setModuleDir('Magento_Test', 'etc', __DIR__ . '/_files/code/Magento/Test/etc');

        /** @var \Magento\Widget\Model\Config\FileResolver $fileResolver */
        $fileResolver = $objectManager->create(
            'Magento\Widget\Model\Config\FileResolver', array(
                'moduleReader' => $moduleReader,
                'applicationDirs' => $dirs,
            )
        );

        $schema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget.xsd';
        $perFileSchema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget_file.xsd';
        $reader = $objectManager->create(
            'Magento\Widget\Model\Config\Reader', array(
                'moduleReader' => $moduleReader,
                'fileResolver' => $fileResolver,
                'schema' => $schema,
                'perFileSchema' => $perFileSchema,
            )
        );

        $this->_configData = $objectManager->create('Magento\Widget\Model\Config\Data', array(
            'reader' => $reader,
        ));
    }

    public function testGet()
    {
        $result = $this->_configData->get();
        $expected = include '_files/expectedGlobalDesignArray.php';
        $this->assertEquals($expected, $result);
    }
}

<?php
/**
 * Magento_Widget_Model_Config_Data
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_disabled.php
 * @magentoAppArea adminhtml
 */
class Magento_Widget_Model_Config_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Widget_Model_Config_Data
     */
    protected $_configData;

    public function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var Magento_Core_Model_Dir $dirs */
        $dirs = $objectManager->create(
            'Magento_Core_Model_Dir', array(
                'baseDir' => BP,
                'dirs' => array(
                    Magento_Core_Model_Dir::MODULES => __DIR__ . '/_files/code',
                    Magento_Core_Model_Dir::CONFIG => __DIR__ . '/_files/code',
                    Magento_Core_Model_Dir::THEMES => __DIR__ . '/_files/design',
                )
            )
        );

        /** @var Magento_Core_Model_Module_Declaration_FileResolver $modulesDeclarations */
        $modulesDeclarations = $objectManager->create(
            'Magento_Core_Model_Module_Declaration_FileResolver', array(
                'applicationDirs' => $dirs,
            )
        );


        /** @var Magento_Core_Model_Module_Declaration_Reader_Filesystem $filesystemReader */
        $filesystemReader = $objectManager->create(
            'Magento_Core_Model_Module_Declaration_Reader_Filesystem', array(
                'fileResolver' => $modulesDeclarations,
            )
        );

        /** @var Magento_Core_Model_ModuleList $modulesList */
        $modulesList = $objectManager->create(
            'Magento_Core_Model_ModuleList', array(
                'reader' => $filesystemReader,
            )
        );

        /** @var Magento_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = $objectManager->create(
            'Magento_Core_Model_Config_Modules_Reader', array(
                'moduleList' => $modulesList
            )
        );
        $moduleReader->setModuleDir('Magento_Test', 'etc', __DIR__ . '/_files/code/Magento/Test/etc');

        /** @var Magento_Widget_Model_Config_FileResolver $fileResolver */
        $fileResolver = $objectManager->create(
            'Magento_Widget_Model_Config_FileResolver', array(
                'moduleReader' => $moduleReader,
                'applicationDirs' => $dirs,
            )
        );

        $schema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget.xsd';
        $perFileSchema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget_file.xsd';
        $reader = $objectManager->create(
            'Magento_Widget_Model_Config_Reader', array(
                'moduleReader' => $moduleReader,
                'fileResolver' => $fileResolver,
                'schema' => $schema,
                'perFileSchema' => $perFileSchema,
            )
        );

        $this->_configData = $objectManager->create('Magento_Widget_Model_Config_Data', array(
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

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Model_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var Magento_Core_Model_Dir $dirs */
        $dirs = $objectManager->create(
            'Magento_Core_Model_Dir', array(
                'baseDir' => BP,
                'dirs' => array(
                    Magento_Core_Model_Dir::MODULES => __DIR__ . '/_files',
                    Magento_Core_Model_Dir::CONFIG => __DIR__ . '/_files'
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
                'dirs' => $dirs,
                'moduleList' => $modulesList
            )
        );

        /** @var Magento_Core_Model_Config_FileResolver $fileResolver */
        $fileResolver = $objectManager->create(
            'Magento_Core_Model_Config_FileResolver', array(
                'moduleReader' => $moduleReader,
            )
        );

        /** @var Magento_Logging_Model_Config_Reader $model */
        $model = $objectManager->create(
            'Magento_GiftRegistry_Model_Config_Reader', array(
                'fileResolver' => $fileResolver,
            )
        );

        $result = $model->read('global');
        $expected = include '_files/giftregistry_config.php';
        $this->assertEquals($expected, $result);



//        $this->_reader = new Magento_GiftRegistry_Model_Config_Reader(
//            $fileResolverMock,
//            $schemaLocatorMock,
//            $validationStateMock,
//        );
    }
}

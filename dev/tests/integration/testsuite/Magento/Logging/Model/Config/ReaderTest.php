<?php
/**
 * Magento_Logging_Model_Config_Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_disabled.php
 */
class Magento_Logging_Model_Config_ReaderTest extends PHPUnit_Framework_TestCase
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
                'moduleList' => $modulesList
            )
        );
        $moduleReader->setModuleDir('Magento_Test', 'etc', __DIR__ . '/_files/Magento/Test/etc');

        /** @var Magento_Core_Model_Config_FileResolver $fileResolver */
        $fileResolver = $objectManager->create(
            'Magento_Core_Model_Config_FileResolver', array(
                'moduleReader' => $moduleReader,
            )
        );

        /** @var Magento_Logging_Model_Config_Reader $model */
        $model = $objectManager->create(
            'Magento_Logging_Model_Config_Reader', array(
                'fileResolver' => $fileResolver,
            )
        );

        $result = $model->read('global');
        $expected = include '_files/expectedArray.php';
        $this->assertEquals($expected, $result);
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = array(
            __DIR__ . '/_files/customerBalance.xml',
            __DIR__ . '/_files/Reward.xml'
        );
        $fileResolverMock = $this->getMockBuilder('Magento_Config_FileResolverInterface')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileResolverMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('logging.xml'), $this->equalTo('global'))
            ->will($this->returnValue($fileList));

        /** @var Magento_Logging_Model_Config_Reader $model */
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create(
            'Magento_Logging_Model_Config_Reader', array(
                'fileResolver' => $fileResolverMock,
            )
        );
        $this->assertArrayHasKey('logging', $model->read('global'));
    }
}

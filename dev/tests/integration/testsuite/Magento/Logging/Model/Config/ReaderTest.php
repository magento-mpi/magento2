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
        /** @var Magento_Core_Model_Dir $dirs */
        $dirs = Mage::getObjectManager()->create(
            'Magento_Core_Model_Dir', array(
                'baseDir' => BP,
                'dirs' => array(
                    Magento_Core_Model_Dir::MODULES => __DIR__ . '/_files',
                    Magento_Core_Model_Dir::CONFIG => __DIR__ . '/_files'
                )
            )
        );

        /** @var Magento_Core_Model_Module_Declaration_FileResolver $modulesDeclarations */
        $modulesDeclarations = Mage::getObjectManager()->create(
            'Magento_Core_Model_Module_Declaration_FileResolver', array(
                'applicationDirs' => $dirs,
            )
        );


        /** @var Magento_Core_Model_Module_Declaration_Reader_Filesystem $filesystemReader */
        $filesystemReader = Mage::getObjectManager()->create(
            'Magento_Core_Model_Module_Declaration_Reader_Filesystem', array(
                'fileResolver' => $modulesDeclarations,
            )
        );

        /** @var Magento_Core_Model_ModuleList $modulesList */
        $modulesList = Mage::getObjectManager()->create(
            'Magento_Core_Model_ModuleList', array(
                'reader' => $filesystemReader,
            )
        );

        /** @var Magento_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Modules_Reader', array(
                'dirs' => $dirs,
                'moduleList' => $modulesList
            )
        );

        /** @var Magento_Core_Model_Config_FileResolver $fileResolver */
        $fileResolver = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_FileResolver', array(
                'moduleReader' => $moduleReader,
            )
        );

        $schema = __DIR__ . '/../../../../../../../../app/code/Magento/Logging/etc/logging.xsd';

        /** @var Magento_Logging_Model_Config_Reader $model */
        $model = Mage::getObjectManager()->create(
            'Magento_Logging_Model_Config_Reader', array(
                'moduleReader' => $moduleReader,
                'fileResolver' => $fileResolver,
                'schema' => $schema
            )
        );

        $result = $model->read('global');
        $expected = include '_files/expectedArray.php';
        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Element 'expected_model': This element is not expected. Expected is ( skip_on_back ).
     */
    public function testMergeCompleteAndPartial()
    {
        $fileList = array(
            __DIR__ . '/_files/customerBalance.xml',
            __DIR__ . '/_files/reward.xml'
        );
        $fileResolverMock = $this->getMockBuilder('Magento_Config_FileResolverInterface')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileResolverMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('logging.xml'), $this->equalTo('global'))
            ->will($this->returnValue($fileList));

        $schema = __DIR__ . '/../../../../../../../../app/code/Magento/Logging/etc/logging.xsd';
        $perFileSchema = __DIR__ . '/../../../../../../../../app/code/Magento/Logging/etc/logging_file.xsd';

        /** @var Magento_Logging_Model_Config_Reader $model */
        $model = Mage::getObjectManager()->create(
            'Magento_Logging_Model_Config_Reader', array(
                'fileResolver' => $fileResolverMock,
                'schema' => $schema,
                'perFileSchema' => $perFileSchema
            )
        );
        $result = $model->read('global');
        $expected = include '_files/expectedArray.php';
        $this->assertEquals($expected, $result);
    }
}
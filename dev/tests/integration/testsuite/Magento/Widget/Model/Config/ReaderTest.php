<?php
/**
 * Magento_Widget_Model_Config_Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_disabled.php
 */
class Magento_Widget_Model_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Widget_Model_Config_Reader
     */
    protected $_model;

    public function setUp()
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

        $schema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget.xsd';
        $perFileSchema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget_file.xsd';
        $this->_model = $objectManager->create(
            'Magento_Widget_Model_Config_Reader', array(
                'moduleReader' => $moduleReader,
                'fileResolver' => $fileResolver,
                'schema' => $schema,
                'perFileSchema' => $perFileSchema,
            )
        );
    }

    public function testRead()
    {
        $result = $this->_model->read('global');
        $expected = include '_files/expectedArray.php';
        $this->assertEquals($expected, $result);
    }

    public function testReadFile()
    {
        $result = $this->_model->readFile(__DIR__ . '/_files/Magento/Test/etc/widget.xml');
        $expected = include '_files/expectedArray.php';
        $this->assertEquals($expected, $result);
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = array(
            __DIR__ . '/_files/widgetFirst.xml',
            __DIR__ . '/_files/widgetSecond.xml'
        );
        $fileResolverMock = $this->getMockBuilder('Magento_Config_FileResolverInterface')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileResolverMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('widget.xml'), $this->equalTo('global'))
            ->will($this->returnValue($fileList));

        $schema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget.xsd';
        $perFileSchema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget_file.xsd';

        /** @var Magento_Widget_Model_Config_Reader $model */
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create(
            'Magento_Widget_Model_Config_Reader', array(
                'fileResolver' => $fileResolverMock,
                'schema' => $schema,
                'perFileSchema' => $perFileSchema
            )
        );
        $model->read('global');
    }
}
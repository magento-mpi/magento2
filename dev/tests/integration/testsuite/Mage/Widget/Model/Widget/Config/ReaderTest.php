<?php
/**
 * Mage_Widget_Model_Config_Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Adminhtml/controllers/_files/cache/all_types_disabled.php
 */
class Mage_Widget_Model_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Widget_Model_Config_Reader
     */
    protected $_model;

    public function setUp()
    {
        /** @var Mage_Core_Model_Dir $dirs */
        $dirs = Mage::getObjectManager()->create(
            'Mage_Core_Model_Dir', array(
                'baseDir' => array(BP),
                'dirs' => array(Mage_Core_Model_Dir::MODULES => __DIR__ . '/_files'))
        );

        /** @var Mage_Core_Model_Module_Declaration_FileResolver $modulesDeclarations */
        $modulesDeclarations = Mage::getObjectManager()->create(
            'Mage_Core_Model_Module_Declaration_FileResolver', array(
                'applicationDirs' => $dirs,
            )
        );


        /** @var Mage_Core_Model_Module_Declaration_Reader_Filesystem $filesystemReader */
        $filesystemReader = Mage::getObjectManager()->create(
            'Mage_Core_Model_Module_Declaration_Reader_Filesystem', array(
                'fileResolver' => $modulesDeclarations,
            )
        );

        /** @var Mage_Core_Model_ModuleList $modulesList */
        $modulesList = Mage::getObjectManager()->create(
            'Mage_Core_Model_ModuleList', array(
                'reader' => $filesystemReader,
            )
        );

        /** @var Mage_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_Modules_Reader', array(
                'dirs' => $dirs,
                'moduleList' => $modulesList
            )
        );

        /** @var Mage_Core_Model_Config_FileResolver $fileResolver */
        $fileResolver = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_FileResolver', array(
                'moduleReader' => $moduleReader,
            )
        );

        $schema = __DIR__ . '/../../../../../../../../../app/code/Mage/Widget/etc/widget.xsd';
        $this->_model = Mage::getObjectManager()->create(
            'Mage_Widget_Model_Config_Reader', array(
                'moduleReader' => $moduleReader,
                'fileResolver' => $fileResolver,
                'schema' => $schema
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
}
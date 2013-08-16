<?php
/**
 * Include verification of overriding service call alias with different classes.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_DataService_Config
     */
    protected $_config;

    public function setUp()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        /** @var Mage_Core_Model_Dir $dirs */
        $dirs = $objectManager->create(
            'Mage_Core_Model_Dir', array(
                'baseDir' => BP,
                'dirs' => array(
                    Mage_Core_Model_Dir::MODULES => __DIR__ . '/_files',
                    Mage_Core_Model_Dir::CONFIG => __DIR__ . '/_files',
                )
            )
        );

        $moduleList = $objectManager->create('Mage_Core_Model_ModuleList', array(
            'reader' => $objectManager->create('Mage_Core_Model_Module_Declaration_Reader_Filesystem', array(
                'fileResolver' => $objectManager->create('Mage_Core_Model_Module_Declaration_FileResolver',
                    array(
                        'applicationDirs' => $dirs
                    )
                )
            )),
            'cache' => $this->getMock('Magento_Config_CacheInterface')
        ));

        /** @var Mage_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = $objectManager->create('Mage_Core_Model_Config_Modules_Reader', array(
            'dirs' => $dirs,
            'moduleList' => $moduleList
        ));

        /** @var Mage_Core_Model_DataService_Config_Reader_Factory $dsCfgReaderFactory */
        $dsCfgReaderFactory = $objectManager->create(
            'Mage_Core_Model_DataService_Config_Reader_Factory');

        $this->_config = new Mage_Core_Model_DataService_Config($dsCfgReaderFactory, $moduleReader);
    }

    public function testGetClassByAliasOverride()
    {
        $classInfo = $this->_config->getClassByAlias('alias');
        $this->assertEquals('last_service', $classInfo['class']);
        $this->assertEquals('last_method', $classInfo['retrieveMethod']);
        $this->assertEquals('last_value', $classInfo['methodArguments']['last_arg']);
        $this->assertEquals('last_value_two', $classInfo['methodArguments']['last_arg_two']);
    }

}

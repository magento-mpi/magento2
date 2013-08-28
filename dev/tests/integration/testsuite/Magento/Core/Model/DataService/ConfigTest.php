<?php
/**
 * Include verification of overriding service call alias with different classes.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_DataService_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_DataService_Config
     */
    protected $_config;

    public function setUp()
    {
        $dirs = new Magento_Core_Model_Dir(__DIR__, array(), array(
            Magento_Core_Model_Dir::MODULES => __DIR__ . '/_files',
            Magento_Core_Model_Dir::CONFIG => __DIR__ . '/_files',
        ));

        $moduleList = Mage::getObjectManager()->create('Magento_Core_Model_ModuleList', array(
            'reader' => Mage::getObjectManager()
                ->create('Magento_Core_Model_Module_Declaration_Reader_Filesystem', array(
                'fileResolver' => Mage::getObjectManager()->create('Magento_Core_Model_Module_Declaration_FileResolver',
                    array(
                        'applicationDirs' => $dirs
                    )
                )
            )),
            'cache' => $this->getMock('Magento_Config_CacheInterface')
        ));

        /** @var Magento_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = Mage::getObjectManager()->create('Magento_Core_Model_Config_Modules_Reader', array(
            'dirs' => $dirs,
            'moduleList' => $moduleList
        ));

        /** @var Magento_Core_Model_DataService_Config_Reader_Factory $dsCfgReaderFactory */
        $dsCfgReaderFactory = Mage::getObjectManager()->create(
            'Magento_Core_Model_DataService_Config_Reader_Factory');

        $this->_config = new Magento_Core_Model_DataService_Config($dsCfgReaderFactory, $moduleReader);
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

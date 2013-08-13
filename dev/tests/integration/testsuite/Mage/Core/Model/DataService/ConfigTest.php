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
        /** @var Mage_Core_Model_Dir $dirs */
        $dirs = Magento_Test_Helper_Bootstrap::getObjectManager()->create(
            'Mage_Core_Model_Dir', array(
                'baseDir' => array(BP),
                'dirs' => array(Mage_Core_Model_Dir::MODULES => __DIR__ . '/_files'))
        );

        /** @var Mage_Core_Model_Config_Loader_Modules $modulesLoader */
        $modulesLoader = Magento_Test_Helper_Bootstrap::getObjectManager()->create(
            'Mage_Core_Model_Config_Loader_Modules', array(
                'dirs' => $dirs
            )
        );

        /**
         * Mock is used to disable caching, as far as Integration Tests Framework loads main
         * modules configuration first and it gets cached
         *
         * @var PHPUnit_Framework_MockObject_MockObject $cache
         */
        $cache = $this->getMock('Mage_Core_Model_Config_Cache', array('load', 'save', 'clean', 'getSection'),
            array(), '', false);
        
        $cache->expects($this->once())
            ->method('load')
            ->will($this->returnValue(false));

        /** @var Mage_Core_Model_Config_Storage $storage */
        $storage = Magento_Test_Helper_Bootstrap::getObjectManager()->create(
            'Mage_Core_Model_Config_Storage', array(
                'loader' => $modulesLoader,
                'cache' => $cache
            )
        );

        $config = new Mage_Core_Model_Config_Base('<config />');
        $modulesLoader->load($config);

        /** @var Mage_Core_Model_Config_Modules $modulesConfig */
        $modulesConfig = Magento_Test_Helper_Bootstrap::getObjectManager()->create(
            'Mage_Core_Model_Config_Modules', array(
                'storage' => $storage
            )
        );

        /** @var Mage_Core_Model_Config_Loader_Modules_File $fileReader */
        $fileReader = Magento_Test_Helper_Bootstrap::getObjectManager()->create(
            'Mage_Core_Model_Config_Loader_Modules_File', array(
                'dirs' => $dirs
            )
        );

        /** @var Mage_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = Magento_Test_Helper_Bootstrap::getObjectManager()->create(
            'Mage_Core_Model_Config_Modules_Reader', array(
                'fileReader' => $fileReader,
                'modulesConfig' => $modulesConfig
            )
        );

        /** @var Mage_Core_Model_DataService_Config_Reader_Factory $dsCfgReaderFactory */
        $dsCfgReaderFactory = Magento_Test_Helper_Bootstrap::getObjectManager()->create(
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
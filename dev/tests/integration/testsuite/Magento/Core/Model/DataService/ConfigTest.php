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
        /** @var Magento_Core_Model_Dir $dirs */
        $dirs = Mage::getObjectManager()->create(
            'Magento_Core_Model_Dir', array(
                'baseDir' => array(BP),
                'dirs' => array(Magento_Core_Model_Dir::MODULES => __DIR__ . '/_files'))
        );

        /** @var Magento_Core_Model_Config_Loader_Modules $modulesLoader */
        $modulesLoader = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Loader_Modules', array(
                'dirs' => $dirs
            )
        );

        /**
         * Mock is used to disable caching, as far as Integration Tests Framework loads main
         * modules configuration first and it gets cached
         *
         * @var PHPUnit_Framework_MockObject_MockObject $cache
         */
        $cache = $this->getMock('Magento_Core_Model_Config_Cache', array('load', 'save', 'clean', 'getSection'),
            array(), '', false);
        
        $cache->expects($this->once())
            ->method('load')
            ->will($this->returnValue(false));

        /** @var Magento_Core_Model_Config_Storage $storage */
        $storage = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Storage', array(
                'loader' => $modulesLoader,
                'cache' => $cache
            )
        );

        $config = new Magento_Core_Model_Config_Base('<config />');
        $modulesLoader->load($config);

        /** @var Magento_Core_Model_Config_Modules $modulesConfig */
        $modulesConfig = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Modules', array(
                'storage' => $storage
            )
        );

        /** @var Magento_Core_Model_Config_Loader_Modules_File $fileReader */
        $fileReader = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Loader_Modules_File', array(
                'dirs' => $dirs
            )
        );

        /** @var Magento_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Modules_Reader', array(
                'fileReader' => $fileReader,
                'modulesConfig' => $modulesConfig
            )
        );

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
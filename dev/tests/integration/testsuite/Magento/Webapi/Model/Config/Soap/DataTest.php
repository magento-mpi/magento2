<?php
/**
 * API Resource config integration tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**#@+
 * Data structures should be available without auto loader as the file name cannot be calculated from class name.
 */
include __DIR__ . '/../../../_files/Model/Webapi/ModuleA/ModuleAData.php';
include __DIR__ . '/../../../_files/Model/Webapi/ModuleA/ModuleADataB.php';
include __DIR__ . '/../../../_files/Controller/Webapi/ModuleA.php';
include __DIR__ . '/../../../_files/Controller/Webapi/SubresourceB.php';
/**#@-*/

/**
 * Class for {@see Magento_Webapi_Model_Config} model testing.
 *
 * The main purpose of this test case is to check config data structure after initialization.
 */
class Magento_Webapi_Model_Config_Soap_DataTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Model_Config_Soap */
    protected $_config;

    /**
     * Set up config with fixture controllers directory scanner.
     */
    protected function setUp()
    {
        $fixtureDir = __DIR__ . '/../../../_files/Controller/Webapi/';
        $directoryScanner = new \Zend\Code\Scanner\DirectoryScanner($fixtureDir);
        /** @var Magento_Core_Model_CacheInterface $cache */
        $cache = $this->getMock('Magento_Core_Model_CacheInterface');
        /** @var Magento_Core_Model_App $app */
        $app = $this->getMockBuilder('Magento_Core_Model_App')->disableOriginalConstructor()->getMock();
        $appConfig = Mage::app()->getConfig();
        $objectManager = Mage::getObjectManager();
        /** @var Magento_Webapi_Helper_Config $helper */
        $helper = $objectManager->get('Magento_Webapi_Helper_Config');
        /** @var Magento_Webapi_Model_Config_Reader_Soap_ClassReflector $classReflector */
        $classReflector = $objectManager->get('Magento_Webapi_Model_Config_Reader_Soap_ClassReflector');
        $cacheState = $objectManager->get('Magento_Core_Model_Cache_StateInterface');
        $moduleList = $objectManager->get('Magento_Core_Model_ModuleListInterface');
        $reader = new Magento_Webapi_Model_Config_Reader_Soap(
            $classReflector, $appConfig, $cache, $moduleList, $cacheState
        );
        $reader->setDirectoryScanner($directoryScanner);

        $this->_config = new Magento_Webapi_Model_Config_Soap($reader, $helper, $app);
        $objectManager->addSharedInstance($this->_config, 'Magento_Webapi_Model_Config_Soap');
    }


    /**
     * Test getResourceDataMerged() functionality.
     * Expected result of method is placed in file fixture.
     */
    public function testGetResource()
    {
        $expectedResourceA = include __DIR__ . '/../../../_files/config/resource_a_fixture.php';
        $this->assertEquals($expectedResourceA, $this->_config->getResourceDataMerged('namespaceAModuleA', 'v1'),
            'Version 1 resource_a data does not match');

        $this->assertEquals(
            include __DIR__ . '/../../../_files/config/resource_a_fixture_v2.php',
            $this->_config->getResourceDataMerged('namespaceAModuleA', 'v2'),
            'Version 2 resource_a data does not match.'
        );

        $this->assertEquals(
            include __DIR__ . '/../../../_files/config/resource_a_subresource_b_fixture.php',
            $this->_config->getResourceDataMerged('namespaceAModuleASubresourceB', 'v1'),
            'Version 1 resource_a_subresource_b data does no match.'
        );
    }

    /**
     * Test getDataType functionality.
     * Expected result of method is placed in file fixture.
     */
    public function testGetDataType()
    {
        $expectedType = include __DIR__ . '/../../../_files/config/data_structure_fixture.php';
        $this->assertEquals($expectedType, $this->_config->getTypeData('NamespaceAModuleAData'));
    }
}

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
    /** @var \Magento\Webapi\Model\Config\Soap */
    protected $_config;

    /**
     * Set up config with fixture controllers directory scanner.
     */
    protected function setUp()
    {
        $fixtureDir = __DIR__ . '/../../../_files/Controller/Webapi/';
        $directoryScanner = new \Zend\Code\Scanner\DirectoryScanner($fixtureDir);
        /** @var \Magento\Core\Model\CacheInterface $cache */
        $cache = $this->getMock('Magento\Core\Model\CacheInterface');
        /** @var \Magento\Core\Model\App $app */
        $app = $this->getMockBuilder('Magento\Core\Model\App')->disableOriginalConstructor()->getMock();
        $appConfig = Mage::app()->getConfig();
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var \Magento\Webapi\Helper\Config $helper */
        $helper = $objectManager->get('Magento\Webapi\Helper\Config');
        /** @var \Magento\Webapi\Model\Config\Reader\Soap\ClassReflector $classReflector */
        $classReflector = $objectManager->get('Magento\Webapi\Model\Config\Reader\Soap\ClassReflector');
        $cacheState = $objectManager->get('Magento\Core\Model\Cache\StateInterface');
        $moduleList = $objectManager->get('Magento\Core\Model\ModuleListInterface');
        $reader = new \Magento\Webapi\Model\Config\Reader\Soap(
            $classReflector, $appConfig, $cache, $moduleList, $cacheState
        );
        $reader->setDirectoryScanner($directoryScanner);

        $this->_config = new \Magento\Webapi\Model\Config\Soap($reader, $helper, $app);
        $objectManager->addSharedInstance($this->_config, '\Magento\Webapi\Model\Config\Soap');
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

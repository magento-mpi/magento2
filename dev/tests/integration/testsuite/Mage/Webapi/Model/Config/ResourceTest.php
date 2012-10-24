<?php
/**
 * API Resource config integration tests.
 *
 * @copyright {}
 */

class Mage_Webapi_Model_Config_ResourceTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Model_Config_Resource */
    protected $_config;

    /**
     * Set up config with fixture controllers directory scanner
     */
    public function setUp()
    {
        $fixtureDir = __DIR__ . '/../../_files/controllers/Webapi/';
        $directoryScanner = new \Zend\Code\Scanner\DirectoryScanner($fixtureDir);

        $this->_config = new Mage_Webapi_Model_Config_Resource(array(
            'directoryScanner' => $directoryScanner
        ));
    }

    /**
     * Test getResource() functionality.
     * Expected result of method is placed in file fixture.
     */
    public function testGetResource()
    {
        $expectedResourceA = include __DIR__ . '/../../_files/config/resource_a_fixture.php';
        $this->assertEquals($expectedResourceA, $this->_config->getResource('namespaceAModuleA', 'v1'),
            'Version 1 resource_a data does not match');

        $expectedResourceAV2 = include __DIR__ . '/../../_files/config/resource_a_fixture_v2.php';
        $this->assertEquals($expectedResourceAV2, $this->_config->getResource('namespaceAModuleA', 'v2'),
            'Version 2 resource_a data does not match.');

        $expectedSubresourceB = include __DIR__ . '/../../_files/config/resource_a_subresource_b_fixture.php';
        $this->assertEquals($expectedSubresourceB, $this->_config->getResource('namespaceAModuleASubresourceB', 'v1'),
            'Version 1 resource_a_subresource_b data does no match.');
    }

    /**
     * Test getDataType functionality.
     * Expected result of method is placed in file fixture.
     */
    public function testGetDataType()
    {
        $expectedType = include __DIR__ . '/../../_files/config/data_structure_fixture.php';
        $this->assertEquals($expectedType, $this->_config->getDataType('NamespaceAModuleADataStructure'));
    }
}

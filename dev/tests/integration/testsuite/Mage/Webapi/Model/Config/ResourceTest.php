<?php
/**
 * API Resource config integration tests.
 *
 * @copyright {}
 */

class Mage_Webapi_Model_Config_ResourceTest extends PHPUnit_Framework_TestCase
{
    public function testGetResource()
    {
        $fixtureDir = __DIR__ . '/../../_files/controllers/Webapi/';
        $directoryScanner = new \Zend\Code\Scanner\DirectoryScanner($fixtureDir);

        $config = new Mage_Webapi_Model_Config_Resource(array(
            'directoryScanner' => $directoryScanner
        ));

        $expectedResourceA = include __DIR__ . '/../../_files/config/resource_a_fixture.php';
        $this->assertEquals($expectedResourceA, $config->getResource('namespaceAModuleAResourceA', 'v1'));
        $expectedSubresourceB = include __DIR__ . '/../../_files/config/resource_a_subresource_b_fixture.php';
        $this->assertEquals($expectedSubresourceB, $config->getResource('namespaceAModuleAResourceASubresourceB', 'v1'));
    }
}

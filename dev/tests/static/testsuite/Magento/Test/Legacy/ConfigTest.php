<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for obsolete and removed config nodes
 */
class Magento_Test_Legacy_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider configFileDataProvider
     */
    public function testConfigFile($file)
    {
        $obsoleteNodes = array();
        $obsoleteNodesFiles = glob(__DIR__ . '/_files/obsolete_config_nodes*.php');
        foreach ($obsoleteNodesFiles as $obsoleteNodesFile) {
            $obsoleteNodes = array_merge($obsoleteNodes, include($obsoleteNodesFile));
        }

        $xml = simplexml_load_file($file);
        foreach ($obsoleteNodes as $xpath => $suggestion) {
            $this->assertEmpty(
                $xml->xpath($xpath),
                "Nodes identified by XPath '$xpath' are obsolete. $suggestion"
            );
        }
    }

    /**
     * @return array
     */
    public function configFileDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getMainConfigFiles();
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for obsolete and removed config nodes
 */
namespace Magento\Test\Legacy;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigFiles()
    {
        $invoker = new \Magento\Framework\Test\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $file
             */
            function ($file) {
                $obsoleteNodes = array();
                $obsoleteNodesFiles = glob(__DIR__ . '/_files/obsolete_config_nodes*.php');
                foreach ($obsoleteNodesFiles as $obsoleteNodesFile) {
                    $obsoleteNodes = array_merge($obsoleteNodes, include $obsoleteNodesFile);
                }

                $xml = simplexml_load_file($file);
                foreach ($obsoleteNodes as $xpath => $suggestion) {
                    $this->assertEmpty(
                        $xml->xpath($xpath),
                        "Nodes identified by XPath '{$xpath}' are obsolete. {$suggestion}"
                    );
                }
            },
            \Magento\Framework\Test\Utility\Files::init()->getMainConfigFiles()
        );
    }
}

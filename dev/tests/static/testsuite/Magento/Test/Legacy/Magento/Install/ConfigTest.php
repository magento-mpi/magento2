<?php
/**
 * Tests for obsolete nodes in install.xml
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Legacy\Magento\Install;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigFile()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            function ($file) {
                $xml = simplexml_load_file($file);
                $path = '/config/check/php/extensions';
                $this->assertEmpty(
                    $xml->xpath($path),
                    "Nodes from '{$path}' in install_wizard.xml have been moved to module.xml"
                );
            },
            \Magento\TestFramework\Utility\Files::init()->getConfigFiles('install_wizard.xml')
        );
    }
}

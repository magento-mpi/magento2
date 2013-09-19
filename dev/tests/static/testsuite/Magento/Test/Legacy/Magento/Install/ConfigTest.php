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
    /**
     * @param string $file
     * @dataProvider configFileDataProvider
     */
    public function testConfigFile($file)
    {
        $xml = simplexml_load_file($file);
        $path = '/config/check/php/extensions';
        $this->assertEmpty(
            $xml->xpath($path),
            "Nodes from '{$path}' in install.xml have been moved module.xml"
        );
    }

    /**
     * @return array
     */
    public function configFileDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getConfigFiles('install.xml');
    }
}

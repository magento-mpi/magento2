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
 * Legacy tests to find obsolete system configuration declaration
 */
class Legacy_ObsoleteSystemConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $configFile
     * @dataProvider systemConfigurationFilesDataProvider
     */
    public function testSystemConfigurationDeclaration($configFile)
    {
        $configXml = simplexml_load_file($configFile);
        $xpath = '/config/tabs|/config/sections';
        $this->assertEmpty(
            $configXml->xpath($xpath),
            'Obsolete system configuration structure detected in file ' . $configFile . '.'
        );
    }

    /**
     * @return array
     */
    public function systemConfigurationFilesDataProvider()
    {
        return Utility_Files::init()->getConfigFiles('system.xml');
    }
}

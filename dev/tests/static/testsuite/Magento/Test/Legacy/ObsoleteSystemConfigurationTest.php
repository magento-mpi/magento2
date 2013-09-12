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
class Magento_Test_Legacy_ObsoleteSystemConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testSystemConfigurationDeclaration()
    {
        $fileList = Magento_TestFramework_Utility_Files::init()->getConfigFiles('system.xml',
            array('wsdl.xml', 'wsdl2.xml', 'wsi.xml'),
            false
        );
        foreach ($fileList as $configFile) {
            $configXml = simplexml_load_file($configFile);
            $xpath = '/config/tabs|/config/sections';
            $this->assertEmpty(
                $configXml->xpath($xpath),
                'Obsolete system configuration structure detected in file ' . $configFile . '.'
            );
        }
    }
}

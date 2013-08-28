<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Legacy tests to find themes non-modular local.xml files declaration
 */
class Magento_Test_Legacy_ObsoleteThemeLocalXmlTest extends PHPUnit_Framework_TestCase
{
    public function testLocalXmlFilesAbsent()
    {
        $area = '*';
        $package = '*';
        $theme = '*';
        $this->assertEmpty(glob(
            Magento_TestFramework_Utility_Files::init()->getPathToSource()
                . "/app/design/{$area}/{$package}/{$theme}/local.xml"
        ));
    }
}

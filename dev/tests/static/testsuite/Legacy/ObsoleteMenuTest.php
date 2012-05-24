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
 * Legacy tests to find obsolete menu declaration
 */
class Legacy_ObsoleteMenuTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $menuFile
     * @dataProvider menuFilesDataProvider
     */
    public function testMenuDeclaration($menuFile)
    {
        $menuXml = simplexml_load_file($menuFile);
        $this->assertEmpty($menuXml->xpath('/config/menu/*/children'), 'Obsolete menu structure detected in file ' . $menuFile . '.');
    }

    /**
     * @return array
     */
    public function menuFilesDataProvider()
    {
        return Utility_Files::init()->getConfigFiles();
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests to verify, that there are no layout files in the folders, where they were before been broken down per handle
 */
class Legacy_ObsoleteLayoutLocationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $location
     * @dataProvider obsoleteLayoutLocationDataProvider
     */
    public function testObsoleteLayoutLocation($location)
    {
        $files = glob($location . '/*.xml');
        $layoutFiles = array();
        foreach ($files as $file) {
            if (strpos(file_get_contents($file), '<layout') !== false) {
                $layoutFiles[] = $file;
            }
        }
        $this->assertEmpty($layoutFiles, 'Obsolete layout files found: ' . implode(', ', $layoutFiles));
    }

    /**
     * @return array
     */
    public static function obsoleteLayoutLocationDataProvider()
    {
        $root = Utility_Files::init()->getPathToSource();
        $modulePaths = glob("{$root}/app/code/*/*/view/*");
        $themePaths = glob("{$root}/app/design/*/*/*");
        $merged = array_merge($modulePaths, $themePaths);

        return Utility_Files::composeDataSets($merged);
    }
}

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
namespace Magento\Test\Legacy;

class ObsoleteLayoutLocationTest extends \PHPUnit_Framework_TestCase
{
    public function testObsoleteLayoutLocation()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            function ($location) {
                $files = glob($location . '/*.xml');
                $layoutFiles = array();
                foreach ($files as $file) {
                    if (strpos(file_get_contents($file), '<layout') !== false) {
                        $layoutFiles[] = $file;
                    }
                }
                $this->assertEmpty($layoutFiles, 'Obsolete layout files found: ' . implode(', ', $layoutFiles));
            },
            self::obsoleteLayoutLocationDataProvider()
        );
    }

    /**
     * @return array
     */
    public static function obsoleteLayoutLocationDataProvider()
    {
        $root = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        $modulePaths = glob("{$root}/app/code/*/*/view/*");
        $themePaths = glob("{$root}/app/design/*/*/*");
        $merged = array_merge($modulePaths, $themePaths);

        return \Magento\TestFramework\Utility\Files::composeDataSets($merged);
    }
}

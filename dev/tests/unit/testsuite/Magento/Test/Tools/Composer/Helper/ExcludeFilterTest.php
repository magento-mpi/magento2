<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\Composer\Helper;

/**
 * Class ExcludeFilterTest
 * @package Magento\Test\Tools\Composer\Helper
 */
class ExcludeFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * ExcludeFilter
     *
     * @var \Magento\Tools\Composer\Helper\ExcludeFilter object
     */
    protected $excludeFilter;

    /**
     * Exclude Array
     *
     * @var Array of excluded paths
     */
    protected $exclude = array();

    /**
     * Initial Setup
     * @return void
     */
    protected function setUp()
    {
        $source = __DIR__ . '/../_files';
        $this->exclude = array(
            str_replace('\\', '/', realpath(__DIR__ . '/../_files/app/code/Magento/OtherModule')),
            str_replace('\\', '/', realpath(__DIR__ . '/../_files/app/code/Magento/SampleModule/etc/module.xml.dist')),
        );

        $directory = new \RecursiveDirectoryIterator($source);

        $this->excludeFilter = new \Magento\Tools\Composer\Helper\ExcludeFilter($directory, $this->exclude);
    }

    /**
     * Test Exclude Filter
     * @return void
     */
    public function testExclude()
    {
        $expected = [
            realpath(__DIR__ . '/../'),
            realpath(__DIR__ . '/../_files'),
            realpath(__DIR__ . '/../_files'),
            realpath(__DIR__ . '/../_files/app'),
            realpath(__DIR__ . '/../_files/app'),
            realpath(__DIR__ . '/../_files/app'),
            realpath(__DIR__ . '/../_files/app/code'),
            realpath(__DIR__ . '/../_files/app/code'),
            realpath(__DIR__ . '/../_files/app/code'),
            realpath(__DIR__ . '/../_files/app/code/Magento'),
            realpath(__DIR__ . '/../_files/app/code/Magento'),
            realpath(__DIR__ . '/../_files/app/code/Magento'),
            realpath(__DIR__ . '/../_files/app/code/Magento/SampleModule'),
            realpath(__DIR__ . '/../_files/app/code/Magento/SampleModule'),
            realpath(__DIR__ . '/../_files/app/code/Magento/SampleModule'),
            realpath(__DIR__ . '/../_files/app/code/Magento/SampleModule/etc'),
            realpath(__DIR__ . '/../_files/app/code/Magento/SampleModule/etc'),
            realpath(__DIR__ . '/../_files/app/code/Magento/SampleModule/etc/module.xml'),
        ];
        $filesIterator = new \RecursiveIteratorIterator($this->excludeFilter, \RecursiveIteratorIterator::SELF_FIRST);
        $files = [];
        foreach ($filesIterator as $file) {
            $files[] = realpath($file);
        }
        sort($files);
        $this->assertSame($expected, $files);
    }
}

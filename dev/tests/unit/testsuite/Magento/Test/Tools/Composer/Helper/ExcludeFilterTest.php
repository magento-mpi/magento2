<?php

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
     * Intial Setup
     * @return void
     */
    protected function setUp()
    {
        $source = __DIR__ . '/../_files';
        $exclude = array(
            realpath(__DIR__ . '/../_files/app/code/Magento/OtherModule')
        );

        $directory = new \RecursiveDirectoryIterator($source);

        $this->excludeFilter = new \Magento\Tools\Composer\Helper\ExcludeFilter($directory, $exclude);
    }

    /**
     * Test Exclude Filter
     * @return void
     */
    public function testExclude()
    {
        $files = new \RecursiveIteratorIterator($this->excludeFilter, \RecursiveIteratorIterator::SELF_FIRST);

        $found = false;

        foreach ($files as $file) {
            if (in_array(realpath($file), $this->exclude)) {
                $found = true;
            }
        }

        $this->assertFalse($found);
    }
}
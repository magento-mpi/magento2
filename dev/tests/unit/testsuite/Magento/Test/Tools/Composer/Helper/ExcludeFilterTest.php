<?php

namespace Magento\Test\Tools\Composer\Helper;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class ExcludeFilterTest
 * @package Magento\Test\Tools\Composer\Helper
 */
class ExcludeFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * ExcludeFilter
     *
     * @var \Magento\Tools\Composer\Helper\FilterTest
     */
    protected $excludeFilter;

    /**
     * Exclude Array
     */
    protected $exclude = array();

    /**
     * Intial Setup
     * @return void
     */
    protected function setUp()
    {
        $objectManagerHelper = new ObjectManager($this);

        $source = __DIR__ . '/../_files';
        $exclude = array(
            realpath(__DIR__ . '/../_files/app/code/Magento/OtherModule')
        );

        $directory = new \RecursiveDirectoryIterator($source);

        $this->excludeFilter = $objectManagerHelper->getObject('\Magento\Tools\Composer\Helper\ExcludeFilter',
            array('iterator' => $directory, 'exclude' => $exclude));
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
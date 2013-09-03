<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class MemoryUsageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Number of application reinitialization iterations to be conducted by tests
     */
    const APP_REINITIALIZATION_LOOPS = 20;

    /**
     * @var Magento_TestFramework_Helper_Memory
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = new Magento_TestFramework_Helper_Memory(new \Magento\Shell);
    }

    /**
     * Test that application reinitialization produces no memory leaks
     */
    public function testAppReinitializationNoMemoryLeak()
    {
        if (extension_loaded('xdebug')) {
            $this->markTestSkipped('Xdebug extension may significantly affect memory consumption of a process.');
        }
        $this->_deallocateUnusedMemory();
        $actualMemoryUsage = $this->_helper->getRealMemoryUsage();
        for ($i = 0; $i < self::APP_REINITIALIZATION_LOOPS; $i++) {
            Magento_TestFramework_Helper_Bootstrap::getInstance()->reinitialize();
            $this->_deallocateUnusedMemory();
        }
        $actualMemoryUsage = $this->_helper->getRealMemoryUsage() - $actualMemoryUsage;
        $this->assertLessThanOrEqual($this->_getAllowedMemoryUsage(), $actualMemoryUsage, sprintf(
            "Application reinitialization causes the memory leak of %u bytes per %u iterations.",
            $actualMemoryUsage,
            self::APP_REINITIALIZATION_LOOPS
        ));
    }

    /**
     * Force to deallocate no longer used memory
     */
    protected function _deallocateUnusedMemory()
    {
        gc_collect_cycles();
    }

    /**
     * Retrieve the allowed memory usage in bytes, depending on the environment
     *
     * @return int
     */
    protected function _getAllowedMemoryUsage()
    {
        // Memory usage limits should not be further increased, corresponding memory leaks have to be fixed instead!
        return Magento_TestFramework_Helper_Memory::convertToBytes('1M');
    }
}

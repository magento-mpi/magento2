<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_MemoryLimitTest extends PHPUnit_Framework_TestCase
{
    public function testPrintHeader()
    {
        $result = Magento_Test_MemoryLimit::printHeader();
        $this->assertNotEmpty($result);
        $this->assertStringEndsWith(PHP_EOL, $result);
    }

    public function testPrintStats()
    {
        $object = $this->_createObject(0, 0);
        $result = $object->printStats();
        $this->assertContains('Memory usage:', $result);
        $this->assertContains('1.00MiB', $result);
        $this->assertContains('Estimated leak:', $result);
        $this->assertContains('Estimated "official" memory usage:', $result);
        $this->assertStringEndsWith(PHP_EOL, $result);

        $object = $this->_createObject('2M', 0);
        $this->assertContains('50.00% of currently configured limit of 2M', $object->printStats());

        $object = $this->_createObject(0, '500K');
        $this->assertContains('% of currently configured limit of 500K', $object->printStats());
    }

    public function testValidateUsage()
    {
        $object = $this->_createObject(0, 0);
        $this->assertNull($object->validateUsage());
    }

    /**
     * @expectedException LogicException
     */
    public function testValidateUsageException()
    {
        $object = $this->_createObject('500K', '2M');
        $object->validateUsage();
    }

    /**
     * @param string $memCap
     * @param string $leakCap
     * @return Magento_Test_MemoryLimit
     */
    protected function _createObject($memCap, $leakCap)
    {
        $helper = $this->getMock('Magento_Test_Helper_Memory', array('getRealMemoryUsage'), array(), '', false);
        $helper->expects($this->any())->method('getRealMemoryUsage')->will($this->returnValue(1024 * 1024));
        return new Magento_Test_MemoryLimit($memCap, $leakCap, $helper);
    }
}

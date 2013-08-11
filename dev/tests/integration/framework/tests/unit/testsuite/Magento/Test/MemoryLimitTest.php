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
        $result = Magento_TestFramework_MemoryLimit::printHeader();
        $this->assertNotEmpty($result);
        $this->assertStringEndsWith(PHP_EOL, $result);
    }

    public function testPrintStats()
    {
        $object = $this->_createObject(0, 0);
        $result = $object->printStats();
        $this->assertContains('Memory usage (OS):', $result);
        $this->assertContains('1.00M', $result);
        $this->assertContains('Estimated memory leak:', $result);
        $this->assertContains('reported by PHP', $result);
        $this->assertStringEndsWith(PHP_EOL, $result);

        $object = $this->_createObject('2M', 0);
        $this->assertContains('50.00% of configured 2.00M limit', $object->printStats());

        $object = $this->_createObject(0, '500K');
        $this->assertContains('% of configured 0.49M limit', $object->printStats());
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
     * @return Magento_TestFramework_MemoryLimit
     */
    protected function _createObject($memCap, $leakCap)
    {
        $helper = $this->getMock('Magento_TestFramework_Helper_Memory', array('getRealMemoryUsage'), array(), '', false);
        $helper->expects($this->any())->method('getRealMemoryUsage')->will($this->returnValue(1024 * 1024));
        return new Magento_TestFramework_MemoryLimit($memCap, $leakCap, $helper);
    }
}

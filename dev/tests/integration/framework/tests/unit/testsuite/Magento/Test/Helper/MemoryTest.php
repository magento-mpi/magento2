<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Helper_MemoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_shell;

    protected function setUp()
    {
        $this->_shell = $this->getMock('Magento_Shell', array('execute'), array(), '', false);
    }

    public function testGetRealMemoryUsage()
    {
        /** @var $mock PHPUnit_Framework_MockObject_MockObject|Magento_Test_Helper_Memory */
        $mock = $this->getMock(
            'Magento_Test_Helper_Memory',
            array('getUnixProcessMemoryUsage', 'getWinProcessMemoryUsage'),
            array($this->_shell)
        );
        $mock->expects($this->any())->method('getUnixProcessMemoryUsage')->will($this->returnValue('gizmo'));
        $mock->expects($this->any())->method('getWinProcessMemoryUsage')->will($this->returnValue('gizmo'));
        $this->assertEquals('gizmo', $mock->getRealMemoryUsage());
    }

    public function testGetUnixProcessMemoryUsage()
    {
        $unixFixture = '  PID USER    PR  NI  VIRT  RES  SHR S %CPU %MEM    TIME+  COMMAND'
            . "\n" . '12345 root    20   0  215m  36m  10m S   98  0.5   0:32.96 php';
        $this->_shell->expects($this->once())->method('execute')->will($this->returnValue($unixFixture));
        $object = new Magento_Test_Helper_Memory($this->_shell);
        $this->assertEquals('37748736', $object->getUnixProcessMemoryUsage(0));
    }

    public function testGetWinProcessMemoryUsage()
    {
        $winFixture = '"php.exe","12345","N/A","0","26,321 K"';
        $this->_shell->expects($this->once())->method('execute')->will($this->returnValue($winFixture));
        $object = new Magento_Test_Helper_Memory($this->_shell);
        $this->assertEquals('26952704', $object->getWinProcessMemoryUsage(0));
    }

    public function testIsWindowsOs()
    {
        $this->assertInternalType('boolean', Magento_Test_Helper_Memory::isWindowsOs());
    }

    /**
     * @param string $number
     * @param string $expected
     * @dataProvider convertToBytesDataProvider
     */
    public function testConvertToBytes($number, $expected)
    {
        $this->assertEquals($expected, Magento_Test_Helper_Memory::convertToBytes($number));
    }

    /**
     * @return array
     */
    public function convertToBytesDataProvider()
    {
        return array(
            array('1B', '1'),
            array('3K', '3072'),
            array('2M', '2097152'),
            array('1G', '1073741824'),
            array('1 234 K', '1263616'),
            array('1 234' . "\xA0" . 'K', '1263616'),
            array('1,234K', '1263616'),
            array('1' . "\xA0" .'234 K', '1263616'),
            array('1.234 K', '1263616'), // this is also correct, accepting that "." is thousands separator
        );
    }

    /**
     * @param string $number
     * @dataProvider convertToBytesBadFormatDataProvider
     * @expectedException InvalidArgumentException
     */
    public function testConvertToBytesBadFormat($number)
    {
        Magento_Test_Helper_Memory::convertToBytes($number);
    }

    /**
     * @return array
     */
    public function convertToBytesBadFormatDataProvider()
    {
        return array(
            'more than one unit of measure' => array('1234KB'),
            'unknown unit of measure' => array('1234Z'),
            'non-integer value' => array('1,234.56 K'),
        );
    }

    /**
     * @param string $number
     * @param string $expected
     * @dataProvider convertToBytes64DataProvider
     */
    public function testConvertToBytes64($number, $expected)
    {
        if (PHP_INT_SIZE <= 4) {
            $this->markTestSkipped("A 64-bit system is required to perform this test.");
        }
        $this->assertEquals($expected, Magento_Test_Helper_Memory::convertToBytes($number));
    }

    /**
     * @return array
     */
    public function convertToBytes64DataProvider()
    {
        return array(
            array('2T', '2199023255552'),
            array('1P', '1125899906842624'),
            array('2E', '2305843009213693952'),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConvertToBytesInvalidArgument()
    {
        Magento_Test_Helper_Memory::convertToBytes('3Z');
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testConvertToBytesOutOfBounds()
    {
        if (PHP_INT_SIZE > 4) {
            $this->markTestSkipped("A 32-bit system is required to perform this test.");
        }
        Magento_Test_Helper_Memory::convertToBytes('2P');
    }
}

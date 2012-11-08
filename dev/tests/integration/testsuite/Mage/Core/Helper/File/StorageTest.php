<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Helper_File_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Helper_File_Storage
     */
    protected $_helper;

    public function setUp()
    {
        $this->_helper = Mage::helper('Mage_Core_Helper_File_Storage');
    }

    protected function tearDown()
    {
        $this->_helper = null;
    }

    /**
     * @covers Mage_Core_Helper_File_Storage::getMaxFileSize
     * @backupStaticAttributes
     */
    public function testGetMaxFileSize()
    {
        $this->assertGreaterThanOrEqual(0, $this->_helper->getMaxFileSize());
        $this->assertGreaterThanOrEqual(0, $this->_helper->getMaxFileSizeInMb());
    }

    /**
     * @covers Mage_Core_Helper_File_Storage::_convertIniToInteger
     * @dataProvider getConvertIniToIntegerDataProvider
     * @backupStaticAttributes
     * @param string $arguments
     * @param int $expected
     */
    public function testConvertIniToInteger($arguments, $expected)
    {
        $class = new ReflectionClass('Mage_Core_Helper_File_Storage');
        $method = $class->getMethod('_convertIniToInteger');
        $method->setAccessible(true);
        $this->assertEquals($expected, $method->invokeArgs($this->_helper, array($arguments)));
    }

    /**
     * @return array
     */
    public function getConvertIniToIntegerDataProvider()
    {
        return array(
            array('0K', 0),
            array('123K', 125952),
            array('1K', 1024),
            array('1g', 1073741824),
            array('asdas', 0),
            array('1M', 1048576),
        );
    }
}

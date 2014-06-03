<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento file size test
 */
namespace Magento\Framework\File;

class SizeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\File\Size
     */
    protected $_fileSize;

    protected function setUp()
    {
        $this->_fileSize = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Framework\File\Size');
    }

    /**
     * @backupStaticAttributes
     */
    public function testGetMaxFileSize()
    {
        $this->assertGreaterThanOrEqual(0, $this->_fileSize->getMaxFileSize());
        $this->assertGreaterThanOrEqual(0, $this->_fileSize->getMaxFileSizeInMb());
    }

    /**
     * @dataProvider getConvertSizeToIntegerDataProvider
     * @backupStaticAttributes
     * @param string $value
     * @param int $expected
     */
    public function testConvertSizeToInteger($value, $expected)
    {
        $this->assertEquals($expected, $this->_fileSize->convertSizeToInteger($value));
    }

    /**
     * @return array
     */
    public function getConvertSizeToIntegerDataProvider()
    {
        return array(
            array('0K', 0),
            array('123K', 125952),
            array('1K', 1024),
            array('1g', 1073741824),
            array('asdas', 0),
            array('1M', 1048576)
        );
    }
}

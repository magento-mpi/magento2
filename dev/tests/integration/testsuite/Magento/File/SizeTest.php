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

/**
 * Magento file size test
 */
namespace Magento\File;

class SizeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\File\Size
     */
    protected $_fileSize;

    public function setUp()
    {
        $this->_fileSize = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\File\Size');
    }

    /**
     * @covers \Magento\Core\Helper\File\Storage::getMaxFileSize
     * @backupStaticAttributes
     */
    public function testGetMaxFileSize()
    {
        $this->assertGreaterThanOrEqual(0, $this->_fileSize->getMaxFileSize());
        $this->assertGreaterThanOrEqual(0, $this->_fileSize->getMaxFileSizeInMb());
    }

    /**
     * @covers \Magento\Core\Helper\File\Storage::convertSizeToInteger
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
            array('1M', 1048576),
        );
    }
}

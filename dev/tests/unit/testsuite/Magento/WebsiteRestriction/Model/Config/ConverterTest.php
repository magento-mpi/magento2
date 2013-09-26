<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Model\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\WebsiteRestriction\Model\Config\Converter
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_filePath;

    protected function setUp()
    {
        $this->_model = new \Magento\WebsiteRestriction\Model\Config\Converter();
        $this->_filePath = realpath(__DIR__)
            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
    }

    public function testConvert()
    {
        $dom = new \DOMDocument();
        $dom->load($this->_filePath . 'webrestrictions.xml');
        $actual = $this->_model->convert($dom);
        $expected = require($this->_filePath . 'webrestrictions.php');
        $this->assertEquals($expected, $actual);
    }
}

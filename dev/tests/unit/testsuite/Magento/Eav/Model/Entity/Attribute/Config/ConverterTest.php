<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Config\Converter
     */
    protected $_model;

    /**
     * Path to files
     *
     * @var string
     */
    protected $_filePath;

    protected function setUp()
    {
        $this->_model = new \Magento\Eav\Model\Entity\Attribute\Config\Converter();
        $this->_filePath = realpath(__DIR__)
            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
    }

    public function testConvert()
    {
        $dom = new \DOMDocument();
        $path = $this->_filePath . 'eav_attributes.xml';
        $dom->load($path);
        $expectedData = include($this->_filePath . 'eav_attributes.php');
        $this->assertEquals($expectedData, $this->_model->convert($dom));
    }
}

<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdminGws\Model\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminGws\Model\Config\Converter
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_fixturePath;

    protected function setUp()
    {
        $this->_model = new \Magento\AdminGws\Model\Config\Converter();
        $this->_fixturePath = realpath(__DIR__) . '/_files/';
    }

    public function testConvert()
    {
        $dom = new \DOMDocument();
        $dom->load($this->_fixturePath . 'adminGws.xml');
        $actual = $this->_model->convert($dom);
        $expected = require $this->_fixturePath . 'adminGws.php';
        $this->assertEquals($expected, $actual);
    }
}

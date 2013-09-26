<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Config_Converter_Dom_FlatTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Config_Converter_Dom_Flat
     */
    protected $_model;

    /**
     * Path to fixtures
     *
     * @var string
     */
    protected $_fixturePath;

    protected function setUp()
    {
        $this->_model = new Magento_Config_Converter_Dom_Flat(array(
            '/root/multipleNode' => 'id'
        ));
        $this->_fixturePath = realpath(__DIR__ . '/../../')
            . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'converter'
            . DIRECTORY_SEPARATOR . 'dom'
            . DIRECTORY_SEPARATOR . 'flat'
            . DIRECTORY_SEPARATOR;
    }

    public function testConvert()
    {
        $expected = require ($this->_fixturePath . 'result.php');

        $dom = new DOMDocument();
        $dom->load($this->_fixturePath . 'source.xml');

        $actual = $this->_model->convert($dom);
        $this->assertEquals($expected, $actual);
    }
}

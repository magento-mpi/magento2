<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Config\Converter\Dom;

class FlatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Config\Converter\Dom\Flat
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
        $this->_model = new \Magento\Config\Converter\Dom\Flat(
            new \Magento\Config\Dom\NodePathConfig(array(
                '/root/multipleNode' => 'id',
                '/root/node_one/subnode' => Flat::ARRAY_KEY_NUMERIC,
            ))
        );
        $this->_fixturePath = realpath(__DIR__ . '/../../')
            . '/_files/converter/dom/flat/';
    }

    public function testConvert()
    {
        $expected = require ($this->_fixturePath . 'result.php');

        $dom = new \DOMDocument();
        $dom->load($this->_fixturePath . 'source.xml');

        $actual = $this->_model->convert($dom);
        $this->assertEquals($expected, $actual);
    }
}

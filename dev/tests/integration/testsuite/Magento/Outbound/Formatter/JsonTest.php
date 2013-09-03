<?php
/**
 * Magento_Outbound_Formatter_Json
 *
 * {license_notice}
 *
 * @copyright          {copyright}
 * @license            {license_link}
 */
require_once __DIR__ . '/_files/Data.php';
class Magento_Outbound_Formatter_JsonTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Outbound_Formatter_Json */
    protected $_formatter;

    public function setUp()
    {
        $this->_formatter = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Outbound_Formatter_Json');
    }

    /**
     * @dataProvider encodeDataProvider
     *
     * @param $body
     * @param $formattedBody
     */
    public function testFormat($body, $formattedBody)
    {
        $this->assertSame($formattedBody, $this->_formatter->format($body));
    }

    /**
     * DataProvider for testing the JSON formatter.
     *
     * @return array
     */
    public function encodeDataProvider()
    {
        return array(
            array(array(), "[]"),
            array(
                array('a' => array('b' => 'c', 'd' => 'e'), 'f' => 'g'), '{"a":{"b":"c","d":"e"},"f":"g"}'
            ),
            array(array(null), '[null]'),
            array(array(true), '[true]'),
            array(array(false), '[false]'),
            array(array(-1), '[-1]'),
            array(array(0), '[0]'),
            array(array(1), '[1]'),
            array(array(2.4), '[2.4]'),
            array(array(2.0), '[2]'),
            array(array(new Data('public', 'protected')), '[{"dataA":"public"}]')
        );
    }
}

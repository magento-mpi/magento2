<?php
/**
 * \Magento\Outbound\Formatter\JsonTest
 *
 * {license_notice}
 *
 * @copyright          {copyright}
 * @license            {license_link}
 */
namespace Magento\Outbound\Formatter;

require_once __DIR__ . '/JsonTest/Data.php';
class JsonTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Outbound\Formatter\Json */
    protected $_formatter;

    public function setUp()
    {
        $this->_formatter = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Outbound\Formatter\Json');
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
                array('a' => array('b' => 'c', 'd' => 'e'), 'f' => 'g'),
                '{"a":{"b":"c","d":"e"},"f":"g"}'
            ),
            array(array(null), '[null]'),
            array(array(true), '[true]'),
            array(array(false), '[false]'),
            array(array(-1), '[-1]'),
            array(array(0), '[0]'),
            array(array(1), '[1]'),
            array(array(2.4), '[2.4]'),
            array(array(2.0), '[2]'),
            array(
                array(new \Magento\Outbound\Formatter\JsonTest\Data('public', 'protected')),
                '[{"dataA":"public"}]'
            )
        );
    }
}

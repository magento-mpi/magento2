<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

/**
 * \Magento\Escaper test case
 */
class EscaperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Escaper
     */
    protected $_escaper = null;

    protected function setUp()
    {
        $this->_escaper = new Escaper();
    }

    /**
     * @covers \Magento\Escaper::escapeHtml
     * @dataProvider escapeHtmlDataProvider
     */
    public function testEscapeHtml($data, $expected)
    {
        $actual = $this->_escaper->escapeHtml($data);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function escapeHtmlDataProvider()
    {
        return array(
            'array data' => array(
                'data' => array('one', '<two>three</two>'),
                'expected' => array('one', '&lt;two&gt;three&lt;/two&gt;')
            ),
            'string data conversion' => array(
                'data' => '<two>three</two>',
                'expected' => '&lt;two&gt;three&lt;/two&gt;'
            ),
            'string data no conversion' => array('data' => 'one', 'expected' => 'one')
        );
    }

    /**
     * @covers \Magento\Escaper::escapeUrl
     */
    public function testEscapeUrl()
    {
        $data = 'http://example.com/search?term=this+%26+that&view=list';
        $expected = 'http://example.com/search?term=this+%26+that&amp;view=list';
        $this->assertEquals($expected, $this->_escaper->escapeUrl($data));
        $this->assertEquals($expected, $this->_escaper->escapeUrl($expected));
    }

    /**
     * @covers \Magento\Escaper::escapeJsQuote
     */
    public function testEscapeJsQuote()
    {
        $data = array("Don't do that.", 'lost_key' => "Can't do that.");
        $expected = array("Don\\'t do that.", "Can\\'t do that.");
        $this->assertEquals($expected, $this->_escaper->escapeJsQuote($data));
        $this->assertEquals($expected[0], $this->_escaper->escapeJsQuote($data[0]));
    }

    /**
     * @covers \Magento\Escaper::escapeQuote
     */
    public function testEscapeQuote()
    {
        $data = "Text with 'single' and \"double\" quotes";
        $expected = array(
            "Text with &#039;single&#039; and &quot;double&quot; quotes",
            "Text with \\&#039;single\\&#039; and \\&quot;double\\&quot; quotes"
        );
        $this->assertEquals($expected[0], $this->_escaper->escapeQuote($data));
        $this->assertEquals($expected[1], $this->_escaper->escapeQuote($data, true));
    }
}

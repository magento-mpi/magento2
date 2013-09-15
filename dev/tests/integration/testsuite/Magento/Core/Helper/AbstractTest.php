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

class Magento_Core_Helper_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Helper\AbstractHelper|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper = null;

    protected function setUp()
    {
        $context = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Helper\Context');
        $this->_helper = $this->getMock('Magento\Core\Helper\AbstractHelper', array('_getModuleName'), array($context));
        $this->_helper
            ->expects($this->any())
            ->method('_getModuleName')
            ->will($this->returnValue('Magento_Core'))
        ;
    }

    /**
     * @covers \Magento\Core\Helper\AbstractHelper::isModuleEnabled
     * @covers \Magento\Core\Helper\AbstractHelper::isModuleOutputEnabled
     */
    public function testIsModuleEnabled()
    {
        $this->assertTrue($this->_helper->isModuleEnabled());
        $this->assertTrue($this->_helper->isModuleOutputEnabled());
    }

    public function test__()
    {
        $uniqueText = uniqid('prefix_');
        $this->assertEquals($uniqueText, __($uniqueText));
    }

    /**
     * @dataProvider escapeHtmlDataProvider
     */
    public function testEscapeHtml($data, $expected)
    {
        $actual = $this->_helper->escapeHtml($data);
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
            'string data no conversion' => array(
                'data' => 'one',
                'expected' => 'one'
            )
        );
    }

    public function testStripTags()
    {
        $this->assertEquals('three', $this->_helper->stripTags('<two>three</two>'));
    }

    /**
     * @covers \Magento\Core\Helper\AbstractHelper::escapeUrl
     */
    public function testEscapeUrl()
    {
        $data = '<two>"three</two>';
        $expected = '&lt;two&gt;&quot;three&lt;/two&gt;';
        $this->assertEquals($expected, $this->_helper->escapeUrl($data));
    }

    public function testJsQuoteEscape()
    {
        $data = array("Don't do that.", 'lost_key' => "Can't do that.");
        $expected = array("Don\\'t do that.", "Can\\'t do that.");
        $this->assertEquals($expected, $this->_helper->jsQuoteEscape($data));
        $this->assertEquals($expected[0], $this->_helper->jsQuoteEscape($data[0]));
    }

    /**
     * @covers \Magento\Core\Helper\AbstractHelper::quoteEscape
     */
    public function testQuoteEscape()
    {
        $data = "Text with 'single' and \"double\" quotes";
        $expected = array(
            "Text with &#039;single&#039; and &quot;double&quot; quotes",
            "Text with \\&#039;single\\&#039; and \\&quot;double\\&quot; quotes",
        );
        $this->assertEquals($expected[0], $this->_helper->quoteEscape($data));
        $this->assertEquals($expected[1], $this->_helper->quoteEscape($data, true));
    }

    public function testUrlEncodeDecode()
    {
        $data = uniqid();
        $result = $this->_helper->urlEncode($data);
        $this->assertNotContains('&', $result);
        $this->assertNotContains('%', $result);
        $this->assertNotContains('+', $result);
        $this->assertNotContains('=', $result);
        $this->assertEquals($data, $this->_helper->urlDecode($result));
    }

    public function testTranslateArray()
    {
        $data = array(uniqid(), array(uniqid(), array(uniqid())));
        $this->assertEquals($data, $this->_helper->translateArray($data));
    }
}

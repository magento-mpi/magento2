<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Core
 */
class Mage_Core_Model_Translate_InlineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Translate_String
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Core_Model_Translate_InlineTest_Mock();
    }

    public function testDisbaledByDefault()
    {
        $model = new Mage_Core_Model_Translate_Inline();
        $this->assertFalse($model->isAllowed());
        $this->assertTrue($this->_model->isAllowed());
    }

    /**
     * Test for Mage_Core_Model_Translate_Inline::stripInlineTranslations
     *
     * @dataProvider dataStripInline
     * @param $body
     * @param $expectedResult
     */
    public function testStripInlineTranslations($body, $expectedResult)
    {
        $this->_model->stripInlineTranslations($body);
        $this->assertEquals($expectedResult, $body);
    }

    public function dataStripInline()
    {
        return array(
            array("{{{1}}{{2}}{{3}}{{4}}}", "1"),
            array('<a title="{{{1}}{{2}}{{3}}{{4}}}">a</a>', '<a title="1">a</a>'),
            array(array('<a title="{{{1}}{{2}}{{3}}{{4}}}">a</a>', '{}'), array('<a title="1">a</a>', '{}')),
        );
    }

    /**
     * Test for Mage_Core_Model_Translate_Inline::processResponseBody
     *
     * @dataProvider dataForProcessResponseBody
     * @param $body
     * @param $expectedResult
     */
    public function testProcessResponseBody($body, $expectedResult)
    {
        $this->_model->processResponseBody($body);
        $this->assertXmlStringEqualsXmlString($expectedResult, $body);
    }

    private function getTranslate($location)
    {
        return htmlspecialchars(json_encode(array(
            'shown' => '1',
            'translated' => '2',
            'original' => '3',
            'location' => $location,
            'scope' => '4',
        )));
    }

    public function dataForProcessResponseBody()
    {
        return array(
            array(
                '{{{1}}{{2}}{{3}}{{4}}}',
                '<span translate="['.$this->getTranslate('Text').']">1</span>'
            ),
            array(
                '<legend>legend</legend>{{{1}}{{2}}{{3}}{{4}}}</b><i><script>{{{1}}{{2}}{{3}}{{4}}}'
                    . '<b title="{{{1}}{{2}}{{3}}{{4}}}">{{{1}}{{2}}{{3}}{{4}}}</b></script></i>',
                '<legend>legend</legend><i><script>1<b title="1">1</b></script>'
                    . '<span class="translate-inline-script" translate="['
                    . $this->getTranslate('String in Javascript').']">SCRIPT</span></i>'
            ),

            // translate in attributes
            array(
                '<a title="{{{1}}{{2}}{{3}}{{4}}}">a</a>',
                '<a translate="['.$this->getTranslate('Tag attribute (ALT, TITLE, etc.)').']" title="1">a</a>'
            ),
            array(
                '<a alt="{{{1}}{{2}}{{3}}{{4}}}" title="{{{1}}{{2}}{{3}}{{4}}}">a</a>',
                '<a translate="['.$this->getTranslate('Tag attribute (ALT, TITLE, etc.)') . ','
                    . $this->getTranslate('Tag attribute (ALT, TITLE, etc.)').']" title="1" alt="1">a</a>'
            ),

            array('<b>{{{1}}{{2}}{{3}}{{4}}}</b>', '<b translate="[' . $this->getTranslate('Bold text') . ']">1</b>'),
            array(
                '<b><i>{{{1}}{{2}}{{3}}{{4}}}</i></b>',
                '<b translate="['.$this->getTranslate('Bold text').']"><i>1</i></b>'
            ),

            array(
                '<title>{{{1}}{{2}}{{3}}{{4}}}</title>',
                '<title>1</title><span class="translate-inline-title" translate="['
                    . $this->getTranslate('Page title') . ']">TITLE</span>'
            ),
            array(
                '<script>{{{1}}{{2}}{{3}}{{4}}}</script>',
                '<script>1</script><span class="translate-inline-script" translate="['
                    . $this->getTranslate('String in Javascript') . ']">SCRIPT</span>'
            ),

            array(
                '<script>{{{1}}{{2}}{{3}}{{4}}}'
                    . 'a = "<b title=\"{{{1}}{{2}}{{3}}{{4}}}\">{{{1}}{{2}}{{3}}{{4}}}</b>";</script>',
                '<script>1a = "<b title="1">1</b>";</script><span class="translate-inline-script" translate="['
                    . $this->getTranslate('String in Javascript') . ']">SCRIPT</span>'
            ),
        );
    }
}

/**
 * Mock class
 */
class Mage_Core_Model_Translate_InlineTest_Mock extends Mage_Core_Model_Translate_Inline
{
    /**
     * Is allowed
     *
     * @param string $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        return true;
    }
}

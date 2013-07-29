<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Translate_PhraseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Translate_Phrase_RendererInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_renderer;

    public function setUp()
    {
        $this->_renderer = $this->getMock('Magento_Translate_Phrase_RendererInterface');
    }

    public function testPhraseCreating()
    {
        $text = 'some text';
        $arguments = array('arg1', 'arg2');

        $phrase = new Magento_Translate_Phrase($text, $arguments);

        $this->assertEquals($text, $phrase->getText());
        $this->assertEquals($arguments, $phrase->getArguments());
    }

    public function testRenderingIfRendererIsNotSet()
    {
        $result = 'some text';
        $phrase = new Magento_Translate_Phrase($result);

        $this->assertEquals($result, (string)$phrase);
    }

    public function testDefersRendering()
    {
        $result = 'rendered text';
        $phrase = new Magento_Translate_Phrase('some text');

        $this->_renderer->expects($this->once())->method('render')->with($phrase)
            ->will($this->returnValue($result));
        Magento_Translate_Phrase::setRenderer($this->_renderer);

        $this->assertEquals($result, (string)$phrase);
    }

    public function testSingleRendering()
    {
        $phrase = new Magento_Translate_Phrase('some text');

        $this->_renderer->expects($this->once())->method('render')->will($this->returnValue('rendered text'));
        Magento_Translate_Phrase::setRenderer($this->_renderer);

        (string)$phrase;
        (string)$phrase;
    }

    public function tearDown()
    {
        $property = new ReflectionProperty('Magento_Translate_Phrase', '_renderer');
        $property->setAccessible(true);
        $property->setValue(null);
    }
}

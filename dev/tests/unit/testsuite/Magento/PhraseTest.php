<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PhraseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Phrase_RendererInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_renderer;

    public function setUp()
    {
        $this->_renderer = $this->getMock('Magento_Phrase_RendererInterface');
        Magento_Phrase::setRenderer($this->_renderer);
    }

    public function testRendering()
    {
        $text = 'some text';
        $arguments = array('arg1', 'arg2');
        $result = 'rendered text';
        $phrase = new Magento_Phrase($text, $arguments);

        $this->_renderer->expects($this->once())->method('render')->with($text, $arguments)
            ->will($this->returnValue($result));

        $this->assertEquals($result, (string)$phrase);
    }

    public function testRenderingIfRendererIsNotSet()
    {
        $result = 'some text';
        $phrase = new Magento_Phrase($result);
        $this->_removeRendererFromPhrase();

        $this->assertEquals($result, (string)$phrase);
    }

    public function testDefersRendering()
    {
        $this->_renderer->expects($this->never())->method('render');

        new Magento_Phrase('some text');
    }

    public function testSingleCallRendering()
    {
        $phrase = new Magento_Phrase('some text');

        $this->_renderer->expects($this->once())->method('render')->will($this->returnValue('rendered text'));

        (string)$phrase;
        (string)$phrase;
    }

    public function tearDown()
    {
        $this->_removeRendererFromPhrase();
    }

    protected function _removeRendererFromPhrase()
    {
        $property = new ReflectionProperty('Magento_Phrase', '_renderer');
        $property->setAccessible(true);
        $property->setValue(null);
    }
}

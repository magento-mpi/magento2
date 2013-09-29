<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

class PhraseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Phrase\RendererInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_renderer;

    protected function setUp()
    {
        $this->_renderer = $this->getMock('Magento\Phrase\RendererInterface');
        \Magento\Phrase::setRenderer($this->_renderer);
    }

    protected function tearDown()
    {
        $this->_removeRendererFromPhrase();
        \Magento\Phrase::setRenderer(new \Magento\Phrase\Renderer\Placeholder());
    }

    public function testRendering()
    {
        $text = 'some text';
        $arguments = array('arg1', 'arg2');
        $result = 'rendered text';
        $phrase = new \Magento\Phrase($text, $arguments);

        $this->_renderer->expects($this->once())->method('render')->with($text, $arguments)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $phrase->render());
    }

    public function testRenderingWithoutRenderer()
    {
        $this->_removeRendererFromPhrase();
        $result = 'some text';
        $phrase = new \Magento\Phrase($result);

        $this->assertEquals($result, $phrase->render());
    }

    public function testDefersRendering()
    {
        $this->_renderer->expects($this->never())->method('render');

        new \Magento\Phrase('some text');
    }

    public function testThatToStringIsAliasToRender()
    {
        $text = 'some text';
        $arguments = array('arg1', 'arg2');
        $result = 'rendered text';
        $phrase = new \Magento\Phrase($text, $arguments);

        $this->_renderer->expects($this->once())->method('render')->with($text, $arguments)
            ->will($this->returnValue($result));

        $this->assertEquals($result, (string)$phrase);
    }

    protected function _removeRendererFromPhrase()
    {
        $property = new \ReflectionProperty('Magento\Phrase', '_renderer');
        $property->setAccessible(true);
        $property->setValue(null);
    }
}

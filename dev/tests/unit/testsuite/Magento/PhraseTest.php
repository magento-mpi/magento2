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
     * @var \Magento\Phrase
     */
    protected $phrase;

    /**
     * @var \Magento\Phrase\RendererInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = $this->getMock('Magento\Phrase\RendererInterface');
        \Magento\Phrase::setRenderer($this->renderer);
    }

    protected function tearDown()
    {
        $this->removeRendererFromPhrase();
        \Magento\Phrase::setRenderer(new \Magento\Phrase\Renderer\Placeholder());
    }

    public function testRendering()
    {
        $text = 'some text';
        $arguments = array('arg1', 'arg2');
        $result = 'rendered text';
        $this->phrase = new \Magento\Phrase($text, $arguments);

        $this->renderer->expects(
            $this->once()
        )->method(
            'render'
        )->with(
            $text,
            $arguments
        )->will(
            $this->returnValue($result)
        );

        $this->assertEquals($result, $this->phrase->render());
    }

    public function testRenderingWithoutRenderer()
    {
        $this->removeRendererFromPhrase();
        $result = 'some text';
        $this->phrase = new \Magento\Phrase($result);

        $this->assertEquals($result, $this->phrase->render());
    }

    public function testDefersRendering()
    {
        $this->renderer->expects($this->never())->method('render');
        $this->phrase = new \Magento\Phrase('some text');
    }

    public function testThatToStringIsAliasToRender()
    {
        $text = 'some text';
        $arguments = array('arg1', 'arg2');
        $result = 'rendered text';
        $this->phrase = new \Magento\Phrase($text, $arguments);

        $this->renderer->expects(
            $this->once()
        )->method(
            'render'
        )->with(
            $text,
            $arguments
        )->will(
            $this->returnValue($result)
        );

        $this->assertEquals($result, (string) $this->phrase);
    }

    protected function removeRendererFromPhrase()
    {
        $property = new \ReflectionProperty('Magento\Phrase', '_renderer');
        $property->setAccessible(true);
        $property->setValue($this->phrase, null);
    }
}

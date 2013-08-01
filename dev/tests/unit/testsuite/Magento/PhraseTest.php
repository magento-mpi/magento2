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
    protected $_defaultRenderer;

    /**
     * @var Magento_Phrase_RendererInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customRenderer;

    public function setUp()
    {
        $this->_defaultRenderer = $this->getMock('Magento_Phrase_RendererInterface');
        $this->_customRenderer = $this->getMock('Magento_Phrase_RendererInterface');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Default renderer is already set
     */
    public function testDefaultRendererIsAlreadySetException()
    {
        Magento_Phrase::setDefaultRenderer($this->_defaultRenderer);
        Magento_Phrase::setDefaultRenderer($this->_defaultRenderer);
    }

    public function testRenderingWithDefaultRendering()
    {
        $text = 'some text';
        $arguments = array('arg1', 'arg2');
        $result = 'rendered text';
        Magento_Phrase::setDefaultRenderer($this->_defaultRenderer);
        $phrase = new Magento_Phrase($text, $arguments);

        $this->_defaultRenderer->expects($this->once())->method('render')->with($text, $arguments)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $phrase->render());
    }

    public function testRenderingWithCustomRendering()
    {
        $text = 'some text';
        $arguments = array('arg1', 'arg2');
        $result = 'rendered text';
        Magento_Phrase::setDefaultRenderer($this->_defaultRenderer);
        $phrase = new Magento_Phrase($text, $arguments);

        $phrase->setCustomRenderer($this->_customRenderer);
        $this->_defaultRenderer->expects($this->never())->method('render');
        $this->_customRenderer->expects($this->once())->method('render')->with($text, $arguments)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $phrase->render());
    }

    public function testRenderingWithoutRenderer()
    {
        $result = 'some text';
        $phrase = new Magento_Phrase($result);

        $this->assertEquals($result, $phrase->render());
    }

    public function testDefersRendering()
    {
        Magento_Phrase::setDefaultRenderer($this->_defaultRenderer);
        $this->_defaultRenderer->expects($this->never())->method('render');

        new Magento_Phrase('some text');
    }

    public function testSingleRendering()
    {
        Magento_Phrase::setDefaultRenderer($this->_defaultRenderer);
        $phrase = new Magento_Phrase('some text');

        $this->_defaultRenderer->expects($this->once())->method('render')
            // method 'will' be used because __toString must return any string
            ->will($this->returnValue('rendered text'));

        $phrase->render();
        $phrase->render();
    }

    public function testRenderingWithSeveralCustomRenderers()
    {
        $text = 'some text';
        $arguments = array('arg1', 'arg2');
        $firstRenderer = $this->_customRenderer;
        $secondRenderer = clone $firstRenderer;
        $resultOfFirstRenderer = 'rendered text';
        $resultOfSecondRenderer = 'rendered text 2';
        $phrase = new Magento_Phrase($text, $arguments);

        $firstRenderer->expects($this->once())->method('render')->with($text, $arguments)
            ->will($this->returnValue($resultOfFirstRenderer));
        $secondRenderer->expects($this->once())->method('render')->with($text, $arguments)
            ->will($this->returnValue($resultOfSecondRenderer));

        $phrase->setCustomRenderer($firstRenderer);
        $this->assertEquals($resultOfFirstRenderer, $phrase->render());

        $phrase->setCustomRenderer($secondRenderer);
        $this->assertEquals($resultOfSecondRenderer, $phrase->render());
    }

    public function testThatToStringIsAliasToRender()
    {
        $text = 'some text';
        $arguments = array('arg1', 'arg2');
        $result = 'rendered text';
        Magento_Phrase::setDefaultRenderer($this->_defaultRenderer);
        $phrase = new Magento_Phrase($text, $arguments);

        $this->_defaultRenderer->expects($this->once())->method('render')->with($text, $arguments)
            ->will($this->returnValue($result));

        $this->assertEquals($result, (string)$phrase);
    }

    protected function _removeRendererFromPhrase()
    {
        $property = new ReflectionProperty('Magento_Phrase', '_defaultRenderer');
        $property->setAccessible(true);
        $property->setValue(null);
    }

    public function tearDown()
    {
        $this->_removeRendererFromPhrase();
    }
}

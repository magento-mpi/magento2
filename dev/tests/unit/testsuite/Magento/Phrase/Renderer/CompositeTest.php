<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_CompositeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Phrase_Renderer_Factory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_rendererFactory;

    protected function setUp()
    {
        $this->_rendererFactory = $this->getMock('Magento_Phrase_Renderer_Factory', array(), array(), '', false);
    }

    /**
     * @param array $renderers
     * @return Magento_Phrase_Renderer_Composite
     */
    protected function _createComposite($renderers = array())
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);

        return $objectManagerHelper->getObject('Magento_Phrase_Renderer_Composite', array(
            'rendererFactory' => $this->_rendererFactory,
            'renderers' => $renderers,
        ));
    }

    public function testCreatingRenderersWhenCompositeCreating()
    {
        $this->_rendererFactory->expects($this->at(0))->method('create')->with('RenderClass1')
            ->will($this->returnValue($this->getMockForAbstractClass('Magento_Phrase_RendererInterface')));
        $this->_rendererFactory->expects($this->at(1))->method('create')->with('RenderClass2')
            ->will($this->returnValue($this->getMockForAbstractClass('Magento_Phrase_RendererInterface')));

        $this->_createComposite(array('RenderClass1', 'RenderClass2'));
    }

    public function testRender()
    {
        $text = 'some text';
        $arguments = array('arg1', 'arg2');
        $resultAfterFirst = 'rendered text first';
        $resultAfterSecond = 'rendered text second';

        $rendererFirst = $this->getMock('Magento_Phrase_RendererInterface');
        $rendererFirst->expects($this->once())->method('render')->with($text, $arguments)
            ->will($this->returnValue($resultAfterFirst));

        $rendererSecond = $this->getMock('Magento_Phrase_RendererInterface');
        $rendererSecond->expects($this->once())->method('render')->with($resultAfterFirst, $arguments)
            ->will($this->returnValue($resultAfterSecond));

        $this->_rendererFactory->expects($this->at(0))->method('create')->with('RenderClass1')
            ->will($this->returnValue($rendererFirst));
        $this->_rendererFactory->expects($this->at(1))->method('create')->with('RenderClass2')
            ->will($this->returnValue($rendererSecond));

        $this->assertEquals(
            $resultAfterSecond,
            $this->_createComposite(array('RenderClass1', 'RenderClass2'))->render($text, $arguments)
        );
    }
}

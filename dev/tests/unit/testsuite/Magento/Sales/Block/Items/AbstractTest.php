<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Items;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testGetItemRenderer()
    {
        $rendererType = 'some-type';
        $renderer = $this->getMock(
            'Magento\View\Element\AbstractBlock',
            array('setRenderedBlock'),
            array(),
            '',
            false
        );

        $rendererList = $this->getMock('Magento\View\Element\RendererList', array(), array(), '', false);
        $rendererList->expects(
            $this->once()
        )->method(
            'getRenderer'
        )->with(
            $rendererType,
            AbstractItems::DEFAULT_TYPE
        )->will(
            $this->returnValue($renderer)
        );

        $layout = $this->getMock('Magento\View\Layout', array('getChildName', 'getBlock'), array(), '', false);

        $layout->expects($this->once())->method('getChildName')->will($this->returnValue('renderer.list'));

        $layout->expects(
            $this->once()
        )->method(
            'getBlock'
        )->with(
            'renderer.list'
        )->will(
            $this->returnValue($rendererList)
        );

        /** @var $block \Magento\Sales\Block\Items\AbstractItems */
        $block = $this->_objectManager->getObject(
            'Magento\Sales\Block\Items\AbstractItems',
            array(
                'context' => $this->_objectManager->getObject(
                    'Magento\Backend\Block\Template\Context',
                    array('layout' => $layout)
                )
            )
        );

        $renderer->expects($this->once())->method('setRenderedBlock')->with($block);

        $this->assertSame($renderer, $block->getItemRenderer($rendererType));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Renderer list for block "" is not defined
     */
    public function te1stGetItemRendererThrowsExceptionForNonexistentRenderer()
    {
        $layout = $this->getMock('Magento\View\Layout', array('getChildName', 'getBlock'), array(), '', false);
        $layout->expects($this->once())->method('getChildName')->will($this->returnValue(null));

        /** @var $block \Magento\Sales\Block\Items\AbstractItems */
        $block = $this->_objectManager->getObject(
            'Magento\Sales\Block\Items\AbstractItems',
            array(
                'context' => $this->_objectManager->getObject(
                    'Magento\Backend\Block\Template\Context',
                    array('layout' => $layout)
                )
            )
        );

        $block->getItemRenderer('some-type');
    }
}

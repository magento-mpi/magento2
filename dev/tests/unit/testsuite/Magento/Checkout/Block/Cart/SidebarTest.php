<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Cart;

class SidebarTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testDeserializeRenders()
    {
        $childBlock = $this->getMock('Magento\View\Element\AbstractBlock', array(), array(), '', false);
        /** @var $layout \Magento\View\LayoutInterface */
        $layout = $this->getMock('Magento\Core\Model\Layout', array(
            'createBlock', 'getChildName', 'setChild'
        ), array(), '', false);

        $rendererList = $this->_objectManager->getObject('Magento\Checkout\Block\Cart\Sidebar', array(
            'context' => $this->_objectManager->getObject('Magento\Backend\Block\Template\Context', array(
                    'layout' => $layout,
                ))
        ));;
        $layout->expects($this->at(0))
            ->method('createBlock')
            ->with('Magento\View\Element\RendererList')
            ->will($this->returnValue($rendererList));
        $layout->expects($this->at(4))
            ->method('createBlock')
            ->with(
                'some-block',
                '.some-template',
                array('data' => array('template' => 'some-type'))
            )
            ->will($this->returnValue($childBlock));
        $layout->expects($this->at(5))
            ->method('getChildName')
            ->with(null, 'some-template')
            ->will($this->returnValue(false));
        $layout->expects($this->at(6))
            ->method('setChild')
            ->with(null, null, 'some-template');

        /** @var $block \Magento\Checkout\Block\Cart\Sidebar */
        $block = $this->_objectManager->getObject('Magento\Checkout\Block\Cart\Sidebar', array(
            'context' => $this->_objectManager->getObject('Magento\Backend\Block\Template\Context', array(
                'layout' => $layout,
            ))
        ));

        $block->deserializeRenders('some-template|some-block|some-type');
    }

    public function testGetIdentities()
    {
        /** @var $block \Magento\Checkout\Block\Cart\Sidebar */
        $block = $this->_objectManager->getObject('Magento\Checkout\Block\Cart\Sidebar');

        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMock('Magento\Catalog\Model\Product', array('__wakeup'), array(), '', false);

        /** @var \Magento\Sales\Model\Quote\Item|\PHPUnit_Framework_MockObject_MockObject $item */
        $item = $this->getMock('Magento\Sales\Model\Quote\Item', array(), array(), '', false);
        $item->expects($this->once())->method('getProduct')->will($this->returnValue($product));

        /** @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject $quote */
        $quote = $this->getMock('Magento\Sales\Model\Quote', array(), array(), '', false);
        $quote->expects($this->once())->method('getAllVisibleItems')->will($this->returnValue(array($item)));

        $block->setData('custom_quote', $quote);
        $this->assertEquals($product->getIdentities(), $block->getIdentities());
    }
}

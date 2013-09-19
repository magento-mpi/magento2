<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Checkout_Block_Cart_AbstractTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_TestFramework_Helper_ObjectManager */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
    }

    public function testGetItemRenderer()
    {
        $renderer = $this->getMock('Magento\Core\Block\AbstractBlock', array('setRenderedBlock'), array(), '', false);
        $layout = $this->getMock('Magento\Core\Model\Layout', array(
            'getChildName', 'getBlock'
        ), array(), '', false);
        $layout->expects($this->at(0))
            ->method('getChildName')
            ->with(null, 'some-type')
            ->will($this->returnValue('some-block-name'));
        $layout->expects($this->at(1))
            ->method('getBlock')
            ->with('some-block-name')
            ->will($this->returnValue($renderer));

        /** @var $block \Magento\Checkout\Block\Cart\AbstractCart */
        $block = $this->_objectManager->getObject('Magento\Checkout\Block\Cart\AbstractCart', array(
            'context' => $this->_objectManager->getObject('Magento\Backend\Block\Template\Context', array(
                'layout' => $layout,
            ))
        ));

        $renderer->expects($this->once())
            ->method('setRenderedBlock')
            ->with($block);

        $this->assertSame($renderer, $block->getItemRenderer('some-type'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Renderer for type "some-type" does not exist.
     */
    public function testGetItemRendererThrowsExceptionForNonexistentRenderer()
    {
        $renderer = $this->getMock('StdClass');
        $layout = $this->getMock('Magento\Core\Model\Layout', array(
            'getChildName', 'getBlock'
        ), array(), '', false);
        $layout->expects($this->at(0))
            ->method('getChildName')
            ->with(null, 'some-type')
            ->will($this->returnValue('some-block-name'));
        $layout->expects($this->at(1))
            ->method('getBlock')
            ->with('some-block-name')
            ->will($this->returnValue($renderer));

        /** @var $block \Magento\Checkout\Block\Cart\AbstractCart */
        $block = $this->_objectManager->getObject('Magento\Checkout\Block\Cart\AbstractCart', array(
            'context' => $this->_objectManager->getObject('Magento\Backend\Block\Template\Context', array(
                'layout' => $layout,
            ))
        ));

        $block->getItemRenderer('some-type');
    }

    public function testPrepareLayout()
    {
        $childBlock = $this->getMock('Magento\Core\Block\AbstractBlock', array(), array(), '', false);
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = $this->getMock('Magento\Core\Model\Layout', array(
            'createBlock', 'getChildName', 'setChild'
        ), array(), '', false);
        $layout->expects($this->once())
            ->method('createBlock')
            ->with(
                'Magento\Checkout\Block\Cart\Item\Renderer',
                '.default',
                array('data' => array('template' => 'cart/item/default.phtml'))
            )
            ->will($this->returnValue($childBlock));
        $layout->expects($this->any())
            ->method('getChildName')
            ->with(null, 'default')
            ->will($this->returnValue(false));
        $layout->expects($this->once())
            ->method('setChild')
            ->with(null, null, 'default');

        /** @var $block \Magento\Checkout\Block\Cart\AbstractCart */
        $block = $this->_objectManager->getObject('Magento\Checkout\Block\Cart\AbstractCart', array(
            'context' => $this->_objectManager->getObject('Magento\Backend\Block\Template\Context', array(
                'layout' => $layout,
            ))
        ));

        $block->setLayout($layout);
    }
}

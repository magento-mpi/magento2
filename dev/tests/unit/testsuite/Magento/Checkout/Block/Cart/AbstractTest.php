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
        $renderer = $this->getMock('Magento_Core_Block_Abstract', array('setRenderedBlock'), array(), '', false);
        $layout = $this->getMock('Magento_Core_Model_Layout', array(
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

        /** @var $block Magento_Checkout_Block_Cart_Abstract */
        $block = $this->_objectManager->getObject('Magento_Checkout_Block_Cart_Abstract', array(
            'context' => $this->_objectManager->getObject('Magento_Backend_Block_Template_Context', array(
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
        $layout = $this->getMock('Magento_Core_Model_Layout', array(
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

        /** @var $block Magento_Checkout_Block_Cart_Abstract */
        $block = $this->_objectManager->getObject('Magento_Checkout_Block_Cart_Abstract', array(
            'context' => $this->_objectManager->getObject('Magento_Backend_Block_Template_Context', array(
                'layout' => $layout,
            ))
        ));

        $block->getItemRenderer('some-type');
    }

    public function testPrepareLayout()
    {
        $childBlock = $this->getMock('Magento_Core_Block_Abstract', array(), array(), '', false);
        /** @var $layout Magento_Core_Model_Layout */
        $layout = $this->getMock('Magento_Core_Model_Layout', array(
            'createBlock', 'getChildName', 'setChild'
        ), array(), '', false);
        $layout->expects($this->once())
            ->method('createBlock')
            ->with(
                'Magento_Checkout_Block_Cart_Item_Renderer',
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

        /** @var $block Magento_Checkout_Block_Cart_Abstract */
        $block = $this->_objectManager->getObject('Magento_Checkout_Block_Cart_Abstract', array(
            'context' => $this->_objectManager->getObject('Magento_Backend_Block_Template_Context', array(
                'layout' => $layout,
            ))
        ));

        $block->setLayout($layout);
    }
}

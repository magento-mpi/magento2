<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Checkout_Block_Cart_SidebarTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Test_Helper_ObjectManager */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new Magento_Test_Helper_ObjectManager($this);
    }

    public function testDeserializeRenders()
    {
        $childBlock = $this->getMock('Magento_Core_Block_Abstract', array(), array(), '', false);
        /** @var $layout Magento_Core_Model_Layout */
        $layout = $this->getMock('Magento_Core_Model_Layout', array(
            'createBlock', 'getChildName', 'setChild'
        ), array(), '', false);
        $layout->expects($this->once())
            ->method('createBlock')
            ->with(
                'some-block',
                '.some-template',
                array('data' => array('template' => 'some-type'))
            )
            ->will($this->returnValue($childBlock));
        $layout->expects($this->once())
            ->method('getChildName')
            ->with(null, 'some-template')
            ->will($this->returnValue(false));
        $layout->expects($this->once())
            ->method('setChild')
            ->with(null, null, 'some-template');

        /** @var $block Magento_Checkout_Block_Cart_Sidebar */
        $block = $this->_objectManager->getObject('Magento_Checkout_Block_Cart_Sidebar', array(
            'context' => $this->_objectManager->getObject('Magento_Backend_Block_Template_Context', array(
                'layout' => $layout,
            ))
        ));

        $block->deserializeRenders('some-template|some-block|some-type');
    }
}

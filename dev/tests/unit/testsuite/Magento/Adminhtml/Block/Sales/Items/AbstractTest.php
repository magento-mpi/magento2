<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Adminhtml_Block_Sales_Items_AbstractTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_TestFramework_Helper_ObjectManager */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
    }

    public function testGetItemRenderer()
    {
        $renderer = $this->getMock('Magento_Core_Block_Abstract', array('addColumnRender'), array(), '', false);
        $renderer->expects($this->at(0))
            ->method('addColumnRender')
            ->with('qty', 'Magento_Adminhtml_Block_Sales_Items_Column_Qty', 'sales/items/column/qty.phtml');
        $renderer->expects($this->at(1))
            ->method('addColumnRender')
            ->with('name', 'Magento_Adminhtml_Block_Sales_Items_Column_Name', 'sales/items/column/name.phtml');
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

        /** @var $block Magento_Adminhtml_Block_Sales_Items_Abstract */
        $block = $this->_objectManager->getObject('Magento_Adminhtml_Block_Sales_Items_Abstract', array(
            'context' => $this->_objectManager->getObject('Magento_Backend_Block_Template_Context', array(
                'layout' => $layout,
            ))
        ));

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

        /** @var $block Magento_Adminhtml_Block_Sales_Items_Abstract */
        $block = $this->_objectManager->getObject('Magento_Adminhtml_Block_Sales_Items_Abstract', array(
            'context' => $this->_objectManager->getObject('Magento_Backend_Block_Template_Context', array(
                'layout' => $layout,
            ))
        ));

        $block->getItemRenderer('some-type');
    }
}

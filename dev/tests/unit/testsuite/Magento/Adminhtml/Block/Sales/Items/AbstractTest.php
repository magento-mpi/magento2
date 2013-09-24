<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Adminhtml_Block_Sales_Items_AbstractTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testGetItemRenderer()
    {
        $renderer = $this->getMock('Magento\Core\Block\AbstractBlock', array('addColumnRender'), array(), '', false);
        $renderer->expects($this->at(0))
            ->method('addColumnRender')
            ->with('qty', 'Magento\Adminhtml\Block\Sales\Items\Column\Qty', 'sales/items/column/qty.phtml');
        $renderer->expects($this->at(1))
            ->method('addColumnRender')
            ->with('name', 'Magento\Adminhtml\Block\Sales\Items\Column\Name', 'sales/items/column/name.phtml');
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

        /** @var $block \Magento\Adminhtml\Block\Sales\Items\AbstractItems */
        $block = $this->_objectManager->getObject('Magento\Adminhtml\Block\Sales\Items\AbstractItems', array(
            'context' => $this->_objectManager->getObject('Magento\Backend\Block\Template\Context', array(
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

        /** @var $block \Magento\Adminhtml\Block\Sales\Items\AbstractItems */
        $block = $this->_objectManager->getObject('Magento\Adminhtml\Block\Sales\Items\AbstractItems', array(
            'context' => $this->_objectManager->getObject('Magento\Backend\Block\Template\Context', array(
                'layout' => $layout,
            ))
        ));

        $block->getItemRenderer('some-type');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Items;

class AbstractItemsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItemRenderer()
    {
        $layout = $this->getMock(
            'Magento\Core\Model\Layout', array('getChildName', 'getBlock', 'getGroupChildNames'), array(), '', false
        );
        $layout->expects($this->any())
            ->method('getChildName')
            ->with(null, 'some-type')
            ->will($this->returnValue('column_block-name'));
        $layout->expects($this->any())
            ->method('getGroupChildNames')
            ->with(null, 'column')
            ->will($this->returnValue(array('column_block-name')));

        $context = $this->getMock('Magento\Backend\Block\Template\Context', array('getLayout'), array(), '', false);
        $context->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $product = $this->getMock('\Magento\Catalog\Model\ProductFactory', array(), array(), '', false);
        $coreData = $this->getMock('\Magento\Core\Helper\Data', array(), array(), '', false);
        $registry = $this->getMock('\Magento\Core\Model\Registry', array(), array(), '', false);

        $renderer = new \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer(
            $product, $coreData, $context, $registry
        );

        $layout->expects($this->any())
            ->method('getBlock')
            ->with('column_block-name')
            ->will($this->returnValue($renderer));

        $block = new \Magento\Sales\Block\Adminhtml\Items\AbstractItems($product, $coreData, $context, $registry);

        $this->assertSame($renderer, $block->getItemRenderer('some-type'));
        $this->assertSame($renderer, $renderer->getColumnRenderer('block-name'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Renderer for type "some-type" does not exist.
     */
    public function testGetItemRendererThrowsExceptionForNonexistentRenderer()
    {
        $renderer = $this->getMock('StdClass');
        $layout = $this->getMock('Magento\Core\Model\Layout', array('getChildName', 'getBlock'), array(), '', false);
        $layout->expects($this->at(0))
            ->method('getChildName')
            ->with(null, 'some-type')
            ->will($this->returnValue('some-block-name'));
        $layout->expects($this->at(1))
            ->method('getBlock')
            ->with('some-block-name')
            ->will($this->returnValue($renderer));

        /** @var $block \Magento\Sales\Block\Adminhtml\Items\AbstractItems */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $block = $objectManager->getObject(
            'Magento\Sales\Block\Adminhtml\Items\AbstractItems',
            array(
                'context' => $objectManager->getObject(
                    'Magento\Backend\Block\Template\Context',
                    array(
                        'layout' => $layout,
                    )
                )
            )
        );

        $block->getItemRenderer('some-type');
    }
}

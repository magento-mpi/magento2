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

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer $renderer */
        $renderer = $helper->getObject('Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer');
        $renderer->setLayout($layout);

        $layout->expects($this->any())
            ->method('getBlock')
            ->with('column_block-name')
            ->will($this->returnValue($renderer));

        /** @var \Magento\Sales\Block\Adminhtml\Items\AbstractItems $block */
        $block = $helper->getObject('Magento\Sales\Block\Adminhtml\Items\AbstractItems');
        $block->setLayout($layout);

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
        $layout = $this->getMock(
            'Magento\Core\Model\Layout', array('getChildName', 'getBlock', '__wakeup'), array(), '', false
        );
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

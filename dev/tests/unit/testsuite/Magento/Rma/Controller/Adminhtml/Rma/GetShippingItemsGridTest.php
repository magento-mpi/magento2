<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class IndexTest extends \Magento\Rma\Controller\Adminhtml\RmaTest
{
    protected $name = 'GetShippingItemsGrid';

    public function testIndexAction()
    {
        $response = 'testResponse';

        $block = $this->getMock(
            'Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping\Grid',
            [],
            [],
            '',
            false
        );
        $block->expects($this->once())
            ->method('toHtml')
            ->willReturn($response);

        $layoutMock = $this->getMock(
            'Magento\Framework\View\Layout',
            ['renderLayout', 'getBlock'],
            [],
            '',
            false
        );
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('magento_rma_getshippingitemsgrid')
            ->will($this->returnValue($block));

        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));


        $this->responseMock->expects($this->once())
            ->method('setBody')
            ->with($response);

        $this->action->execute();
    }
}

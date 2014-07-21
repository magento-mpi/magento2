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
    protected $name = 'Index';

    public function testIndexAction()
    {
        $layoutMock = $this->getMock(
            'Magento\Framework\View\Layout',
            ['renderLayout', 'getBlock'],
            [],
            '',
            false
        );
        $blockMock = $this->getMock('Magento\Backend\Block\Menu', ['setActive', 'getMenuModel'], [], '', false);
        $menuModelMock = $this->getMock('Magento\Backend\Model\Menu', [], [], '', false);
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('menu')
            ->will($this->returnValue($blockMock));
        $blockMock->expects($this->once())
            ->method('setActive')
            ->with('Magento_Rma::sales_magento_rma_rma');
        $blockMock->expects($this->once())
            ->method('getMenuModel')
            ->will($this->returnValue($menuModelMock));
        $menuModelMock->expects($this->once())
            ->method('getParentItems')
            ->will($this->returnValue([]));
        $this->titleMock->expects($this->once())
            ->method('add')
            ->with(__('Returns'));
        $this->viewMock->expects($this->once())
            ->method('renderLayout');

        $this->assertNull($this->action->execute());
    }
}

<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class LoadNewAttributesTest extends \Magento\Rma\Controller\Adminhtml\RmaTest
{
    protected $name = 'LoadNewAttributes';

    public function testLoadNewAttributesActionWithoutUserAttributes()
    {
        $productId = 1;
        $rmaMock = $this->getMock('Magento\Rma\Model\Item', [], [], '', false);
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $blockMock = $this->getMock(
            'Magento\Framework\View\Element\Template',
            ['setProductId', 'initForm'],
            [],
            '',
            false
        );

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('product_id', null)
            ->will($this->returnValue($productId));
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Rma\Model\Item', [])
            ->will($this->returnValue($rmaMock));
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));


        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('magento_rma_edit_item')
            ->will($this->returnValue($blockMock));
        $blockMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->will($this->returnSelf());
        $blockMock->expects($this->once())
            ->method('initForm')
            ->will($this->returnSelf());

        $this->responseMock->expects($this->never())
            ->method('setBody');

        $this->assertNull($this->action->execute());
    }

    public function testLoadNewAttributeActionResponseArray()
    {
        $productId = 1;
        $responseArray = ['html', 'html'];
        $responseString = 'json';
        $rmaMock = $this->getMock('Magento\Rma\Model\Item', [], [], '', false);
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $blockMock = $this->getMock(
            'Magento\Framework\View\Element\Template',
            ['setProductId', 'initForm'],
            [],
            '',
            false
        );

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('product_id', null)
            ->will($this->returnValue($productId));
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Rma\Model\Item', [])
            ->will($this->returnValue($rmaMock));
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));


        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('magento_rma_edit_item')
            ->will($this->returnValue($blockMock));
        $blockMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->will($this->returnSelf());

        $blockMock->expects($this->once())
            ->method('initForm')
            ->will($this->returnValue($this->formMock));

        $this->formMock->expects($this->once())
            ->method('hasNewAttributes')
            ->will($this->returnValue(true));
        $this->formMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($responseArray));
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Core\Helper\Data')
            ->will($this->returnValue($this->helperMock));
        $this->helperMock->expects($this->once())
            ->method('jsonEncode')
            ->with($responseArray)
            ->will($this->returnValue($responseString));
        $this->responseMock->expects($this->once())
            ->method('representJson')
            ->with($responseString);
        $this->responseMock->expects($this->never())
            ->method('setBody');
        $this->assertNull($this->action->execute());
    }

    public function testLoadNewAttributesActionResponseString()
    {
        $productId = 1;
        $responseString = 'json';
        $rmaMock = $this->getMock('Magento\Rma\Model\Item', [], [], '', false);
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $blockMock = $this->getMock(
            'Magento\Framework\View\Element\Template',
            ['setProductId', 'initForm'],
            [],
            '',
            false
        );

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('product_id', null)
            ->will($this->returnValue($productId));
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Rma\Model\Item', [])
            ->will($this->returnValue($rmaMock));
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));


        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('magento_rma_edit_item')
            ->will($this->returnValue($blockMock));
        $blockMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->will($this->returnSelf());

        $blockMock->expects($this->once())
            ->method('initForm')
            ->will($this->returnValue($this->formMock));

        $this->formMock->expects($this->once())
            ->method('hasNewAttributes')
            ->will($this->returnValue(true));
        $this->formMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($responseString));
        $this->helperMock->expects($this->never())
            ->method('jsonEncode');
        $this->responseMock->expects($this->never())
            ->method('representJson');
        $this->responseMock->expects($this->once())
            ->method('setBody')
            ->with($responseString);
        $this->assertNull($this->action->execute());
    }
}

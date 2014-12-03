<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Dashboard;

/**
 * Base assertion test method for CustomerMost,CustomerNewest,ProductsViewed test classes
 */
class BaseAssertion extends \PHPUnit_Framework_TestCase
{
    protected function assertExecute($controllerName, $blockName)
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $outPut = "data";
        $resultRawMock = $this->getMock(
            'Magento\Framework\Controller\Result\Raw',
            ['setContents'], [], '', false);
        $resultRawFactoryMock = $this->getMock(
            'Magento\Framework\Controller\Result\RawFactory',
            ['create'], [], '', false);
        $layoutFactoryMock = $this->getMock(
            'Magento\Framework\View\LayoutFactory',
            ['create', 'createBlock', 'toHtml'], [], '', false);
        $layoutFactoryMock->expects($this->once())->method('create')->will($this->returnSelf());
        $layoutFactoryMock->expects($this->once())->method('createBlock')->with($blockName)->will($this->returnSelf());
        $layoutFactoryMock->expects($this->once())->method('toHtml')->will($this->returnValue($outPut));
        $resultRawFactoryMock->expects($this->once())->method('create')->will($this->returnValue($resultRawMock));
        $resultRawMock->expects($this->once())->method('setContents')->with($outPut)->will($this->returnSelf());

        $controller = $objectManager->getObject(
            $controllerName,
            [
                'resultRawFactory' => $resultRawFactoryMock,
                'layoutFactory' => $layoutFactoryMock
            ]
        );
        $result = $controller->execute();
        $this->assertInstanceOf('Magento\Framework\Controller\Result\Raw', $result);
    }
}
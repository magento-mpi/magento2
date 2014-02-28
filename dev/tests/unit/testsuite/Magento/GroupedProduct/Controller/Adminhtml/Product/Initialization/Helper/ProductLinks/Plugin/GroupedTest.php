<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Controller\Adminhtml\Product\Initialization\Helper\ProductLinks\Plugin;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GroupedProduct\Controller\Adminhtml\Product\Initialization\Helper\ProductLinks\Plugin\Grouped
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->productMock= $this->getMock('Magento\Catalog\Model\Product',
            array('getGroupedReadonly', 'setGroupedLinkData', '__wakeup'), array(), '', false);
        $this->subjectMock =
            $this->getMock('Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper\ProductLinks',
                array(), array(), '', false);
        $this->model = new Grouped($this->requestMock);
    }

    public function testAfterInitializeLinksRequestDoesNotHaveGrouped()
    {
        $this->requestMock->expects($this->once())->method('getPost')->with('links')->will($this->returnValue(array()));
        $this->productMock->expects($this->never())->method('getGroupedReadonly');
        $this->productMock->expects($this->never())->method('setGroupedLinkData');
        $this->assertEquals($this->productMock,
            $this->model->afterInitializeLinks($this->subjectMock, $this->productMock)
        );
    }

    public function testAfterInitializeLinksRequestHasGrouped()
    {
        $this->requestMock->expects($this->once())
            ->method('getPost')
            ->with('links')
            ->will($this->returnValue(array('grouped' => 'value')));

        $this->productMock->expects($this->once())
            ->method('getGroupedReadonly')
            ->will($this->returnValue(false));
        $this->productMock->expects($this->once())->method('setGroupedLinkData')->with(array('value'));
        $this->assertEquals($this->productMock,
            $this->model->afterInitializeLinks($this->subjectMock, $this->productMock)
        );
    }

    public function testAfterInitializeLinksProductIsReadonly()
    {
        $this->requestMock->expects($this->once())
            ->method('getPost')
            ->with('links')
            ->will($this->returnValue(array('grouped' => 'value')));

        $this->productMock->expects($this->once())
            ->method('getGroupedReadonly')
            ->will($this->returnValue(true));
        $this->productMock->expects($this->never())->method('setGroupedLinkData');
        $this->assertEquals($this->productMock,
            $this->model->afterInitializeLinks($this->subjectMock, $this->productMock)
        );
    }
}

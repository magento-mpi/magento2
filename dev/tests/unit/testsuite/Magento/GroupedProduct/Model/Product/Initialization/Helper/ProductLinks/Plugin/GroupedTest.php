<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GroupedProduct\Model\Product\Initialization\Helper\ProductLinks\Plugin;

use Magento\GroupedProduct\Model\Product\Initialization\Helper\ProductLinks\Plugin\Grouped;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GroupedProduct\Model\Product\Initialization\Helper\ProductLinks\Plugin\Grouped
     */
    protected $model;

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
        $this->productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('getGroupedReadonly', 'setGroupedLinkData', '__wakeup'),
            array(),
            '',
            false
        );
        $this->subjectMock = $this->getMock(
            'Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks',
            array(),
            array(),
            '',
            false
        );
        $this->model = new Grouped();
    }

    public function testAfterInitializeLinksRequestDoesNotHaveGrouped()
    {
        $this->productMock->expects($this->never())->method('getGroupedReadonly');
        $this->productMock->expects($this->never())->method('setGroupedLinkData');
        $this->assertEquals(
            $this->productMock,
            $this->model->afterInitializeLinks($this->subjectMock, $this->productMock, array())
        );
    }

    public function testAfterInitializeLinksRequestHasGrouped()
    {
        $this->productMock->expects($this->once())->method('getGroupedReadonly')->will($this->returnValue(false));
        $this->productMock->expects($this->once())->method('setGroupedLinkData')->with(array('value'));
        $this->assertEquals(
            $this->productMock,
            $this->model->afterInitializeLinks($this->subjectMock, $this->productMock, array('grouped' => 'value'))
        );
    }

    public function testAfterInitializeLinksProductIsReadonly()
    {
        $this->productMock->expects($this->once())->method('getGroupedReadonly')->will($this->returnValue(true));
        $this->productMock->expects($this->never())->method('setGroupedLinkData');
        $this->assertEquals(
            $this->productMock,
            $this->model->afterInitializeLinks($this->subjectMock, $this->productMock, array('grouped' => 'value'))
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler\ProductType;

class DownloadableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Downloadable
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    protected function setUp()
    {
        $this->productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('getDownloadableData', 'getTypeInstance', 'setDownloadableData', 'getTypeId', '__wakeup'),
            array(),
            '',
            false
        );
        $this->model = new Downloadable();
    }

    public function testHandleWithNonDownloadableProductType()
    {
        $this->productMock->expects($this->once())->method('getTypeId')->will($this->returnValue('some product type'));
        $this->productMock->expects($this->never())->method('getDownloadableData');
        $this->model->handle($this->productMock);
    }

    public function testHandleWithoutDownloadableLinks()
    {
        $this->productMock->expects(
            $this->once()
        )->method(
            'getTypeId'
        )->will(
            $this->returnValue(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
        );
        $this->productMock->expects($this->once())->method('getDownloadableData')->will($this->returnValue(array()));

        $this->productMock->expects($this->never())->method('setDownloadableData');
        $this->model->handle($this->productMock);
    }

    public function testHandleWithoutDownloadableData()
    {
        $this->productMock->expects(
            $this->once()
        )->method(
            'getTypeId'
        )->will(
            $this->returnValue(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
        );
        $this->productMock->expects($this->once())->method('getDownloadableData')->will($this->returnValue(null));

        $this->productMock->expects($this->never())->method('setDownloadableData');
        $this->model->handle($this->productMock);
    }

    public function testHandleWithDownloadableData()
    {
        $linkMock = $this->getMock(
            '\Magento\Downloadable\Model\Link',
            array('getPrice', '__wakeup'),
            array(),
            '',
            false
        );
        $linkMock->expects($this->any())->method('getPrice')->will($this->returnValue(100500));
        $links = array('1' => $linkMock, '2' => $linkMock);
        $downloadableData = array(
            'link' => array(
                array('link_id' => 1, 'is_delete' => false),
                array('link_id' => 2, 'is_delete' => true),
                array('link_id' => 3, 'is_delete' => false)
            )
        );
        $expected = array(
            'link' => array(
                array('link_id' => 1, 'is_delete' => false, 'price' => 100500),
                array('link_id' => 2, 'is_delete' => true, 'price' => 0),
                array('link_id' => 3, 'is_delete' => false, 'price' => 0)
            )
        );

        $this->productMock->expects(
            $this->once()
        )->method(
            'getTypeId'
        )->will(
            $this->returnValue(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
        );
        $this->productMock->expects(
            $this->once()
        )->method(
            'getDownloadableData'
        )->will(
            $this->returnValue($downloadableData)
        );

        $typeMock = $this->getMock('\Magento\Downloadable\Model\Product\Type', array(), array(), '', false);
        $typeMock->expects($this->once())->method('getLinks')->will($this->returnValue($links));
        $this->productMock->expects($this->once())->method('getTypeInstance')->will($this->returnValue($typeMock));

        $this->productMock->expects($this->once())->method('setDownloadableData')->with($expected);
        $this->model->handle($this->productMock);
    }
}

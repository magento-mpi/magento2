<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model;

class AbstractItemsTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Sales\Model\Order\Item|\PHPUnit_Framework_MockObject_MockObject */
    protected $orderItem;
    /** @var \Magento\Bundle\Model\Sales\Order\Pdf\Items\Shipment $model */
    protected $model;

    protected function setUp()
    {
        $this->orderItem = $this->getMock(
            'Magento\Sales\Model\Order\Item',
            array('getProductOptions', '__wakeup', 'getParentItem', 'getOrderItem'),
            array(),
            '',
            false
        );

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Bundle\Model\Sales\Order\Pdf\Items\Shipment');
    }

    /**
     * @dataProvider isShipmentSeparatelyWithoutItemDataProvider
     */
    public function testIsShipmentSeparatelyWithoutItem($productOptions, $result)
    {
        $this->model->setItem($this->orderItem);
        $this->orderItem->expects($this->any())->method('getProductOptions')->will($this->returnValue($productOptions));

        $this->assertSame($result, $this->model->isShipmentSeparately());
    }

    public function isShipmentSeparatelyWithoutItemDataProvider()
    {
        return array(
            array(array('shipment_type' => 1), true),
            array(array('shipment_type' => 0), false),
            array(array(), false)
        );
    }

    /**
     * @dataProvider isShipmentSeparatelyWithItemDataProvider
     */
    public function testIsShipmentSeparatelyWithItem($productOptions, $result, $parentItem)
    {
        if ($parentItem) {
            $parentItem = $this->getMock(
                'Magento\Sales\Model\Order\Item',
                array('getProductOptions', '__wakeup'),
                array(),
                '',
                false
            );
            $parentItem->expects($this->any())->method('getProductOptions')->will($this->returnValue($productOptions));
        } else {
            $this->orderItem->expects($this->any())->method('getProductOptions')
                ->will($this->returnValue($productOptions));
        }
        $this->orderItem->expects($this->any())->method('getParentItem')->will($this->returnValue($parentItem));
        $this->orderItem->expects($this->any())->method('getOrderItem')->will($this->returnSelf());

        $this->assertSame($result, $this->model->isShipmentSeparately($this->orderItem));
    }

    public function isShipmentSeparatelyWithItemDataProvider()
    {
        return array(
            array(array('shipment_type' => 1), false, false),
            array(array('shipment_type' => 0), true, false),
            array(array('shipment_type' => 1), true, true),
            array(array('shipment_type' => 0), false, true),
        );
    }

    /**
     * @dataProvider isChildCalculatedWithoutItemDataProvider
     */
    public function testIsChildCalculatedWithoutItem($productOptions, $result)
    {
        $this->model->setItem($this->orderItem);
        $this->orderItem->expects($this->any())->method('getProductOptions')->will($this->returnValue($productOptions));

        $this->assertSame($result, $this->model->isChildCalculated());
    }

    public function isChildCalculatedWithoutItemDataProvider()
    {
        return array(
            array(array('product_calculations' => 0), true),
            array(array('product_calculations' => 1), false),
            array(array(), false)
        );
    }

    /**
     * @dataProvider isChildCalculatedWithItemDataProvider
     */
    public function testIsChildCalculatedWithItem($productOptions, $result, $parentItem)
    {
        if ($parentItem) {
            $parentItem = $this->getMock(
                'Magento\Sales\Model\Order\Item',
                array('getProductOptions', '__wakeup'),
                array(),
                '',
                false
            );
            $parentItem->expects($this->any())->method('getProductOptions')->will($this->returnValue($productOptions));
        } else {
            $this->orderItem->expects($this->any())->method('getProductOptions')
                ->will($this->returnValue($productOptions));
        }
        $this->orderItem->expects($this->any())->method('getParentItem')->will($this->returnValue($parentItem));
        $this->orderItem->expects($this->any())->method('getOrderItem')->will($this->returnSelf());

        $this->assertSame($result, $this->model->isChildCalculated($this->orderItem));
    }

    public function isChildCalculatedWithItemDataProvider()
    {
        return array(
            array(array('product_calculations' => 0), false, false),
            array(array('product_calculations' => 1), true, false),
            array(array('product_calculations' => 0), true, true),
            array(array('product_calculations' => 1), false, true),
        );
    }

    /**
     * @dataProvider getBundleOptionsDataProvider
     */
    public function testGetBundleOptions($productOptions, $result)
    {
        $this->model->setItem($this->orderItem);
        $this->orderItem->expects($this->any())->method('getProductOptions')->will($this->returnValue($productOptions));
        $this->assertSame($result, $this->model->getBundleOptions());
    }

    public function getBundleOptionsDataProvider()
    {
        return array(
            array(array('bundle_options' => 'result'), 'result'),
            array(array(), array()),
        );
    }

    /**
     * @dataProvider getSelectionAttributesDataProvider
     */
    public function testGetSelectionAttributes($productOptions, $result)
    {
        $this->orderItem->expects($this->any())->method('getProductOptions')->will($this->returnValue($productOptions));
        $this->assertSame($result, $this->model->getSelectionAttributes($this->orderItem));
    }

    public function getSelectionAttributesDataProvider()
    {
        return array(
            array(array(), null),
        );
    }

    public function testGetOrderOptions()
    {
        $productOptions = array(
            'options' => array('options'),
            'additional_options' => array('additional_options'),
            'attributes_info' => array('attributes_info')
        );
        $this->model->setItem($this->orderItem);
        $this->orderItem->expects($this->any())->method('getProductOptions')->will($this->returnValue($productOptions));
        $this->assertEquals(array('attributes_info', 'options', 'additional_options'), $this->model->getOrderOptions());
    }

    public function testGetOrderItem()
    {
        $this->model->setItem($this->orderItem);
        $this->assertSame($this->orderItem, $this->model->getOrderItem());
    }

    /**
     * @dataProvider canShowPriceInfoDataProvider
     */
    public function testCanShowPriceInfo($parentItem, $productOptions, $result)
    {
        $this->model->setItem($this->orderItem);
        $this->orderItem->expects($this->any())->method('getOrderItem')->will($this->returnSelf());
        $this->orderItem->expects($this->any())->method('getParentItem')->will($this->returnValue($parentItem));
        $this->orderItem->expects($this->any())->method('getProductOptions')->will($this->returnValue($productOptions));

        $this->assertSame($result, $this->model->canShowPriceInfo($this->orderItem));
    }

    public function canShowPriceInfoDataProvider()
    {
        return array(
            array(true, array('product_calculations' => 0), true),
            array(false, array(), true),
            array(false, array('product_calculations' => 0), false),
        );
    }
}

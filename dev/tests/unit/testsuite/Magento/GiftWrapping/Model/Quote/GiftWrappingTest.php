<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Model\Quote;

/**
 * Test class for \Magento\GiftWrapping\Model\Quote\Giftwrapping
 */
class GiftWrappingTest extends \PHPUnit_Framework_TestCase
{
    protected $model;

    public function setUp()
    {
        $this->model = $this->getMockBuilder('Magento\GiftWrapping\Model\Total\Quote\Giftwrapping')
            ->disableOriginalConstructor()
            ->setMethods(array('_getAddressItems', '_getWrapping'))
            ->getMock();
    }

    /**
     * @param $store
     * @param $item
     * @param $address
     * @dataProvider quoteItemWrappingWithProductDataProvider
     */
    public function testQuoteItemWrappingWithProduct($store, $item, $address)
    {
        $this->model->expects($this->any())
            ->method('_getAddressItems')
            ->will($this->returnValue(array($item)));
        $this->model->expects($this->any())
            ->method('_getWrapping')
            ->will($this->returnValue($item->getWrapping()));

        $storeProperty = new \ReflectionProperty($this->model, '_store');
        $storeProperty->setAccessible(true);
        $storeProperty->setValue($this->model, $store);

        $method = new \ReflectionMethod(get_class($this->model), '_collectWrappingForItems');
        $method->setAccessible(true);
        $method->invoke($this->model, $address);

        $this->assertEquals(20, $address->getGwItemsBasePrice());
        $this->assertEquals(10, $item->getGwBasePrice());
        $this->assertEquals(20, $address->getGwItemsPrice());
        $this->assertEquals(10, $item->getGwPrice());
    }

    public function quoteItemWrappingWithProductDataProvider()
    {
        return $this->_prepareData(true);
    }

    /**
     * @param $store
     * @param $item
     * @param $address
     * @dataProvider quoteItemWrappingWithoutProductDataProvider
     */
    public function testQuoteItemWrappingWithoutProduct($store, $item, $address)
    {
        $this->model->expects($this->any())
            ->method('_getAddressItems')
            ->will($this->returnValue(array($item)));
        $wrapping = new \Magento\Object(array('base_price' => 6));
        $item->setWrapping($wrapping);
        $this->model->expects($this->any())
            ->method('_getWrapping')
            ->will($this->returnValue($item->getWrapping()));

        $storeProperty = new \ReflectionProperty($this->model, '_store');
        $storeProperty->setAccessible(true);
        $storeProperty->setValue($this->model, $store);

        $method = new \ReflectionMethod(get_class($this->model), '_collectWrappingForItems');
        $method->setAccessible(true);
        $method->invoke($this->model, $address);

        $this->assertEquals(12, $address->getGwItemsBasePrice());
        $this->assertEquals(6, $item->getGwBasePrice());
        $this->assertEquals(20, $address->getGwItemsPrice());
        $this->assertEquals(10, $item->getGwPrice());
    }

    public function quoteItemWrappingWithoutProductDataProvider()
    {
        return $this->_prepareData(false);
    }

    protected function _prepareData($withProduct)
    {
        $item = new \Magento\Object();
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(array('isVirtual', '__wakeup'))
            ->getMock();

        $product->expects($this->any())
            ->method('isVirtual')
            ->will($this->returnValue(false));

        $product->setGiftWrappingPrice(($withProduct) ? 10 : 0);

        $item->setProduct($product)
            ->setQty(2)
            ->setGwId(1);

        $address = $this->getMockBuilder('Magento\Sales\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->setMethods(array('getAllItems', 'setId', '__wakeup'))
            ->getMock();
        $address->expects($this->any())
            ->method('getAllItems')
            ->will($this->returnValue(array($item)));

        $store = $this->getMockBuilder('Magento\Core\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(array('convertPrice', '__wakeup'))
            ->getMock();

        $store->expects($this->any())
            ->method('convertPrice')
            ->will($this->returnValue(10));

        return [
            [$store, $item, $address]
        ];
    }
}
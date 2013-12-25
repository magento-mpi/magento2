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

namespace Magento\GiftWrapping\Model\Quote\Tax;

/**
 * Test class for \Magento\GiftWrapping\Model\Quote\Tax\Giftwrapping
 */
class GiftWrappingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $store
     * @param $item
     * @param $address
     * @dataProvider quoteItemTaxWrappingDataProvider
     */
    public function testQuoteItemTaxWrapping($item, $address)
    {
        $model = $this->getMockBuilder('Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping')
            ->disableOriginalConstructor()
            ->setMethods(array('_getAddressItems', '_getWrapping', '_calcTaxAmount'))
            ->getMock();
        $item->setGwBasePrice(10)
            ->setGwPrice(5);
        $model->expects($this->any())
            ->method('_getAddressItems')
            ->will($this->returnValue([$item]));
        $model->expects($this->any())
            ->method('_getWrapping')
            ->will($this->returnValue($item->getWrapping()));
        $model->expects($this->any())
            ->method('_calcTaxAmount')
            ->will($this->returnArgument(0));

        $method = new \ReflectionMethod(get_class($model), '_collectWrappingForItems');
        $method->setAccessible(true);
        $method->invoke($model, $address);

        $this->assertEquals(20, $address->getGwItemsBaseTaxAmount());
        $this->assertEquals(10, $item->getGwBaseTaxAmount());
        $this->assertEquals(10, $address->getGwItemsTaxAmount());
        $this->assertEquals(5, $item->getGwTaxAmount());
    }

    public function quoteItemTaxWrappingDataProvider()
    {
        $item = new \Magento\Object();
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(array('isVirtual', '__wakeup'))
            ->getMock();
        $product->expects($this->any())
            ->method('isVirtual')
            ->will($this->returnValue(false));
        $product->setGiftWrappingPrice(10);

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

        return [
            [$item, $address]
        ];

    }
}
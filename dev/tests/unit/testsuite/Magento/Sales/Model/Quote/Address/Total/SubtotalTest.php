<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Quote\Address\Total;

class SubtotalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Quote\Address\Total\Subtotal
     */
    protected $subtotalModel;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subtotalModel = $objectManager->getObject('Magento\Sales\Model\Quote\Address\Total\Subtotal');
    }

    public function collectDataProvider()
    {
        return array(
            array(12, 10, false, true, true),
            array(12, 0, false, true, true),
            array(0, 10, false, true, true),
            array(12, 10, true, false, false),
            array(12, 10, false, false, true),
        );
    }

    /**
     * @dataProvider collectDataProvider
     */
    public function testCollect($finalPrice, $originalPrice, $hasParent, $isChildrenCalculated, $isTotalCalculated)
    {
        /** @var \Magento\Sales\Model\Quote\Item|\PHPUnit_Framework_MockObject_MockObject $quoteItem */
        $quoteItem = $this->getMock(
            'Magento\Sales\Model\Quote\Item',
            array(),
            array(),
            '',
            false
        );
        /** @var \Magento\Sales\Model\Quote\Address|\PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMock(
            'Magento\Sales\Model\Quote\Address',
            array(),
            array(),
            '',
            false
        );
        $address->expects($this->any())->method('getAllNonNominalItems')->will(
            $this->returnValue(array($quoteItem))
        );

        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            array(),
            array(),
            '',
            false
        );
        $product->expects($this->any())->method('getPrice')->will($this->returnValue($originalPrice));
        $quoteItem->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        /** @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject $quote */
        $quote = $this->getMock(
            'Magento\Sales\Model\Quote',
            array(),
            array(),
            '',
            false
        );

        $address->expects($this->any())->method('getAllNonNominalItems')->will(
            $this->returnValue(array($quoteItem))
        );
        $quoteItem->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $address->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $product->expects($this->any())->method('isVisibleInCatalog')->will($this->returnValue(true));
        $parentQuoteItem = $this->getMock(
            'Magento\Sales\Model\Quote\Item',
            array(),
            array(),
            '',
            false
        );
        if (!$hasParent) {
            $parentQuoteItem = false;
        }
        $quoteItem->expects($this->any())->method('getParentItem')->will($this->returnValue($parentQuoteItem));
        $quoteItem->expects($this->any())->method('isChildrenCalculated')->will(
            $this->returnValue($isChildrenCalculated)
        );

        if ($isTotalCalculated) {
            if ($hasParent && $isChildrenCalculated) {
                $parentQuoteItem->expects($this->any())->method('getProduct')->will($this->returnValue($product));
                $priceModel = $this->getMock('\Magento\Catalog\Model\Product\Type\Price', array(), array(), '', false);
                $priceModel->expects($this->any())->method('getChildFinalPrice')->will(
                    $this->returnValue($finalPrice)
                );
                $product->expects($this->once())->method('getPriceModel')->will(
                    $this->returnValue($priceModel)
                );
            } else if(!$hasParent) {
                $product->expects($this->any())->method('getFinalPrice')->will($this->returnValue($finalPrice));
            }
            $quoteItem->expects($this->once())->method('setPrice')->with($this->equalTo($finalPrice))->will(
                $this->returnValue($quoteItem)
            );
            $quoteItem->expects($this->once())->method('calcRowTotal');
        } else {
            $quoteItem->expects($this->never())->method('calcRowTotal');
        }

        $this->subtotalModel->collect($address);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Block\Sku;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class ProductsTest
 */
class ProductsTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\AdvancedCheckout\Block\Sku\Products */
    protected $products;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\AdvancedCheckout\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $checkoutHelperMock;

    /** @var \Magento\CatalogInventory\Service\V1\StockItemService|\PHPUnit_Framework_MockObject_MockObject */
    protected $stockItemMock;

    protected function setUp()
    {
        $this->checkoutHelperMock = $this->getMock('Magento\AdvancedCheckout\Helper\Data', [], [], '', false);
        $this->checkoutHelperMock->expects($this->once())
            ->method('getFailedItems')
            ->will($this->returnValue([]));
        $this->stockItemMock = $this->getMock(
            'Magento\CatalogInventory\Service\V1\StockItemService',
            [],
            [],
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->products = $this->objectManagerHelper->getObject(
            'Magento\AdvancedCheckout\Block\Sku\Products',
            [
                'checkoutData' => $this->checkoutHelperMock,
                'stockItemService' => $this->stockItemMock
            ]
        );
    }

    /**
     * @param array $config
     * @param bool $result
     * @dataProvider showItemLinkDataProvider
     */
    public function testShowItemLink($config, $result)
    {
        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $product->expects($this->once())
            ->method('isComposite')
            ->will($this->returnValue($config['is_composite']));

        if ($config['is_composite']) {
            $productsInGroup = [
                [$this->getChildProductMock($config['is_in_stock'])]
            ];

            $typeInstance = $this->getMock(
                'Magento\Catalog\Model\Product\Type\Simple',
                [],
                [],
                '',
                false
            );
            $typeInstance->expects($this->once())
                ->method('getProductsToPurchaseByReqGroups')
                ->with($this->equalTo($product))
                ->will($this->returnValue($productsInGroup));

            $product->expects($this->once())
                ->method('getTypeInstance')
                ->will($this->returnValue($typeInstance));
        }

        $quoteItem = $this->getMock('Magento\Sales\Model\Quote\Item', [], [], '', false);
        $quoteItem->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $this->assertSame($result, $this->products->showItemLink($quoteItem));
    }

    /**
     * @param bool $isInStock
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getChildProductMock($isInStock)
    {
        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['hasStockItem', 'isDisabled', 'getId', '__wakeup'],
            [],
            '',
            false
        );
        $product->expects($this->once())
            ->method('hasStockItem')
            ->will($this->returnValue(true));
        if ($isInStock) {
            $product->expects($this->once())
                ->method('isDisabled')
                ->will($this->returnValue(false));
        }
        $product->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(10));

        $this->stockItemMock->expects($this->once())
            ->method('getIsInStock')
            ->with($this->equalTo(10))
            ->will($this->returnValue($isInStock));
        return $product;
    }

    /**
     * @return array
     */
    public function showItemLinkDataProvider()
    {
        return [
            [
                ['is_composite' => false], true
            ],
            [
                ['is_composite' => true, 'is_in_stock' => true], true
            ],
            [
                ['is_composite' => true, 'is_in_stock' => false], false
            ],
        ];
    }
}

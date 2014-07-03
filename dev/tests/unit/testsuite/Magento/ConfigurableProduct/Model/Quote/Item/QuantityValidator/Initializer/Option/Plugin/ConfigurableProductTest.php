<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Model\Quote\Item\QuantityValidator\Initializer\Option\Plugin;

class ConfigurableProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $data
     * @dataProvider aroundGetStockItemDataProvider
     */
    public function testAroundGetStockItem(array $data)
    {
        $subjectMock = $this->getMock(
            'Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option',
            array(),
            array(),
            '',
            false
        );

        $quoteItemMock = $this->getMock(
            'Magento\Sales\Model\Quote\Item', array('getProductType', '__wakeup'), array(), '', false
        );
        $quoteItemMock->expects($this->once())
            ->method('getProductType')
            ->will($this->returnValue($data['product_type']));

        $stockItemMock = $this->getMock(
            'Magento\CatalogInventory\Model\Stock\Item', array('setProductName', '__wakeup'), array(), '', false
        );
        $matcherMethod = $data['matcher_method'];
        $stockItemMock->expects($this->$matcherMethod())
            ->method('setProductName');

        $optionMock = $this->getMock(
            'Magento\Sales\Model\Quote\Item\Option', array('getProduct', '__wakeup'), array(), '', false
        );

        $proceed = function () use ($stockItemMock) {
            return $stockItemMock;
        };

        $model = new ConfigurableProduct;
        $model->aroundGetStockItem($subjectMock, $proceed, $optionMock, $quoteItemMock, 0);
    }

    /**
     * @return array
     */
    public function aroundGetStockItemDataProvider()
    {
        return array(
            array(
                array(
                    'product_type' => 'not_configurable',
                    'matcher_method' => 'never'
                )
            ),
            array(
                array(
                    'product_type' => 'configurable',
                    'matcher_method' => 'once'
                )
            )
        );
    }
}

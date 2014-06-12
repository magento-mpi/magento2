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
     * @dataProvider beforeInitializeDataProvider
     */
    public function testBeforeInitialize(array $data)
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

        $productMock = $this->getMock(
            'Magento\Catalog\Model\Product', array('getStockItem', '__wakeup'), array(), '', false
        );
        $productMock->expects($this->once())
            ->method('getStockItem')
            ->will($this->returnValue($stockItemMock));

        $optionMock = $this->getMock(
            'Magento\Sales\Model\Quote\Item\Option', array('getProduct', '__wakeup'), array(), '', false
        );
        $optionMock->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($productMock));

        $model = new ConfigurableProduct;
        $model->beforeInitialize($subjectMock, $optionMock, $quoteItemMock, 0);

    }

    /**
     * @return array
     */
    public function beforeInitializeDataProvider()
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

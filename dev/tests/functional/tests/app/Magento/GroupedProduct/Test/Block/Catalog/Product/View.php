<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Block\Catalog\Product;

use Magento\Catalog\Test\Block\Product\View as ParentView;

/**
 * Class View
 * Grouped product view block on the product page
 */
class View extends ParentView
{
    /**
     * Block grouped product
     *
     * @var string
     */
    protected $blockGroupedProduct = '.wrapper.table.grouped';

    /**
     * This member holds the class name of the tier price block.
     *
     * @var string
     */
    protected $formatTierPrice = "//tr[%row-number%]//ul[contains(@class,'tier')]//*[@class='item'][%line-number%]";

    /**
     * This member holds the class name of the special price block.
     *
     * @var string
     */
    protected $formatSpecialPrice = ".product.info.main tr:nth-child(%row-number%) .price-box";

    /**
     * Get grouped product block
     *
     * @return \Magento\GroupedProduct\Test\Block\Catalog\Product\View\Type\Grouped
     */
    public function getGroupedProductBlock()
    {
        return $this->blockFactory->create(
            'Magento\GroupedProduct\Test\Block\Catalog\Product\View\Type\Grouped',
            [
                'element' => $this->_rootElement->find($this->blockGroupedProduct)
            ]
        );
    }

    /**
     * Change tier price selector
     *
     * @param int $index
     * @return void
     */
    public function itemTierPriceProductBlock($index)
    {
        $this->tierPricesSelector = str_replace('%row-number%', $index, $this->formatTierPrice);
    }

    /**
     * Change tier price selector
     *
     * @param int $index
     * @return void
     */
    public function itemPriceProductBlock($index)
    {
        $this->priceBlock = str_replace('%row-number%', $index, $this->formatSpecialPrice);
    }
}

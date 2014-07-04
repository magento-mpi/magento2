<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product\Compare\ListCompare;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Interceptor
 * Compare product block
 */
class Interceptor extends Block
{
    /**
     * Selector by product info block
     *
     * @var string
     */
    protected $productBlockInfo = '//td[contains(@class, "cell product info")][%d]';

    /**
     * Selector by product attribute block
     *
     * @var string
     */
    protected $productBlockAttribute = '//tr[td[contains(@class,"attribute")]][%d]';

    /**
     * Selector by name product
     *
     * @var string
     */
    protected $nameSelector = './/*[contains(@class, "product name")]/a';

    /**
     * Selector by price product
     *
     * @var string
     */
    protected $priceSelector = './/span[@class="price"]/span';

    /**
     * Selector by sku product
     *
     * @var string
     */
    protected $attributeSelector = './td[%d]/div';

    /**
     * Remove button selector
     *
     * @var string
     */
    protected $removeButton = 'a.action.delete';

    /**
     * Get product name
     *
     * @param int $index
     * @return string
     */
    public function getProductName($index)
    {
        return $this->getCompareProductInfoBlock($index)->find($this->nameSelector, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get product price
     *
     * @param int $index
     * @param string $currency [optional]
     * @return string
     */
    public function getProductPrice($index, $currency = '$')
    {
        $infoBlock = $this->getCompareProductInfoBlock($index);
        if (!$infoBlock->find($this->priceSelector, Locator::SELECTOR_XPATH)->isVisible()) {
            $this->priceSelector = './/span[@class="price"]';
        }
        return trim($infoBlock->find($this->priceSelector, Locator::SELECTOR_XPATH)->getText(), $currency);
    }

    /**
     * Get product sku
     *
     * @param int $index
     * @return string
     */
    public function getProductSku($index)
    {
        return $this->getAttribute(1, $index);
    }

    /**
     * Get product description
     *
     * @param int $index
     * @return string
     */
    public function getProductDescription($index)
    {
        return $this->getAttribute(2, $index);
    }

    /**
     * Get product short description
     *
     * @param int $index
     * @return string
     */
    public function getProductShortDescription($index)
    {
        return $this->getAttribute(3, $index);
    }

    /**
     * Get item compare product info block
     *
     * @param int $index
     * @return Element
     */
    protected function getCompareProductInfoBlock($index)
    {
        return $this->_rootElement->find(sprintf($this->productBlockInfo, $index), Locator::SELECTOR_XPATH);
    }

    /**
     * Get item compare product attribute block
     *
     * @param int $index
     * @return Element
     */
    protected function getCompareProductAttributeBlock($index)
    {
        return $this->_rootElement->find(sprintf($this->productBlockAttribute, $index), Locator::SELECTOR_XPATH);
    }

    /**
     * Get item attribute
     *
     * @param int $indexAttribute
     * @param int $indexProduct
     * @return string
     */
    protected function getAttribute($indexAttribute, $indexProduct)
    {
        return trim(
            $this->getCompareProductAttributeBlock($indexAttribute)
                ->find(sprintf($this->attributeSelector, $indexProduct), Locator::SELECTOR_XPATH)->getText()
        );
    }

    /**
     * Remove product from compare product list
     *
     * @return void
     */
    public function removeProduct()
    {
        $this->_rootElement->find($this->removeButton)->click();
    }
}

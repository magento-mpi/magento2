<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product\Compare;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class ListCompare
 * Compare list product block
 */
class ListCompare extends Block
{
    /**
     * Selector by product info
     *
     * @var string
     */
    protected $productInfo = '//td[contains(@class, "cell product info")][%d]';

    /**
     * Selector by product attribute
     *
     * @var string
     */
    protected $productAttribute = '//tr[th//*[normalize-space(text()) = "%s"]]';

    /**
     * Selector by name product
     *
     * @var string
     */
    protected $nameSelector = './/*[contains(@class, "product name")]/a';

    /**
     * Selector for search product via name
     *
     * @var string
     */
    protected $productName = '[normalize-space(text()) = "%s"]';

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
    protected $removeButton = 'thead td:nth-child(%d) a.action.delete';

    /**
     * Selector for  "Clear All" button
     *
     * @var string
     */
    protected $clearAll = 'a.action.clear';

    /**
     * Selector for empty message
     *
     * @var string
     */
    protected $isEmpty = 'p.empty';

    /**
     * Get product info
     *
     * @param int $index
     * @param string $attributeKey
     * @param string $currency
     * @return string
     */
    public function getProductInfo($index, $attributeKey, $currency = '$')
    {
        $infoBlock = $this->getCompareProductInfo($index);
        if ($attributeKey == 'price') {
            if (!$infoBlock->find($this->priceSelector, Locator::SELECTOR_XPATH)->isVisible()) {
                $this->priceSelector = './/span[@class="price"]';
            }
            return trim($infoBlock->find($this->priceSelector, Locator::SELECTOR_XPATH)->getText(), $currency);
        } else {
            return $infoBlock->find($this->nameSelector, Locator::SELECTOR_XPATH)->getText();
        }
    }

    /**
     * Get item compare product info
     *
     * @param int $index
     * @return Element
     */
    protected function getCompareProductInfo($index)
    {
        return $this->_rootElement->find(sprintf($this->productInfo, $index), Locator::SELECTOR_XPATH);
    }

    /**
     * Get item compare product attribute
     *
     * @param string $key
     * @return Element
     */
    protected function getCompareProductAttribute($key)
    {
        return $this->_rootElement->find(sprintf($this->productAttribute, $key), Locator::SELECTOR_XPATH);
    }

    /**
     * Get item attribute
     *
     * @param int $indexProduct
     * @param string $attributeKey
     * @return string
     */
    public function getProductAttribute($indexProduct, $attributeKey)
    {
        return trim(
            $this->getCompareProductAttribute($attributeKey)
                ->find(sprintf($this->attributeSelector, $indexProduct), Locator::SELECTOR_XPATH)->getText()
        );
    }

    /**
     * Remove product from compare product list
     *
     * @param int $index
     * @return void
     */
    public function removeProduct($index = 2)
    {
        $this->_rootElement->find(sprintf($this->removeButton, $index))->click();
    }

    /**
     * Click " Clear All" on "My Account" page
     *
     * @return void
     */
    public function clickClearAll()
    {
        $this->_rootElement->find($this->clearAll)->click();
        $this->_rootElement->acceptAlert();
    }

    /**
     * Get empty message on compare product block
     *
     * @param string $productName
     * @return string|bool
     */
    public function productIsNotInBlock($productName = '')
    {
        $isEmpty = $this->_rootElement->find($this->isEmpty);
        if ($isEmpty->isVisible()) {
            return $isEmpty->getText();
        }
        $nameSelector = $this->nameSelector . sprintf($this->productName, $productName);
        $isVisible = $this->_rootElement->find($nameSelector, Locator::SELECTOR_XPATH)->isVisible();
        if ($productName !== '' && $isVisible) {
            return $this->_rootElement->find($nameSelector, Locator::SELECTOR_XPATH)->getText();
        }
        return true;
    }
}

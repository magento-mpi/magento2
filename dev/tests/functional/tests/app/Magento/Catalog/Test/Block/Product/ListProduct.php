<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class SearchResultsList
 * Product list
 *
 * @package Magento\Catalog\Test\Block\Product
 */
class ListProduct extends Block
{
    /**
     * Product name
     *
     * @var string
     */
    protected $productTitle = '.product.name';

    /**
     * Click for Price link on category page
     *
     * @var string
     */
    protected $clickForPrice = "//div[contains(@class, 'product details') and ('%s')]//a[contains(@id, 'msrp-click')]";

    /**
     * MAP popup on Category page
     *
     * @var string
     */
    protected $mapPopup = '#map-popup';

    /**
     * Minimum Advertised Price on category page
     *
     * @var string
     */
    protected $oldPrice = "[id*=product-price]";

    /**
     * Check if product with specified name is visible
     *
     * @param string $productName
     * @return bool
     */
    public function isProductVisible($productName)
    {
        return $this->_rootElement->find($this->productTitle, Locator::SELECTOR_CSS)
            ->find('//*[@title="' . $productName .'"]', Locator::SELECTOR_XPATH)
            ->isVisible();
    }

    /**
     * Open product view page by clicking on product name
     *
     * @param string $productName
     */
    public function openProductViewPage($productName)
    {
        $this->_rootElement->find($this->productTitle, Locator::SELECTOR_CSS)
            ->find('//*[@title="' . $productName . '"]', Locator::SELECTOR_XPATH)
            ->click();
    }

    /**
     * Open MAP block on category page
     */
    public function openMapBlockOnCategoryPage($productName)
    {
        $this->_rootElement->find(sprintf($this->clickForPrice, $productName), Locator::SELECTOR_XPATH)->click();
        $this->waitForElementVisible($this->mapPopup, Locator::SELECTOR_CSS);
    }

    /**
     * Get Minimum Advertised Price on Category page
     *
     * @return array|string
     */
    public function getOldPriceCategoryPage()
    {
        return $this->_rootElement->find($this->oldPrice, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Retrieve product price by specified Id
     *
     * @param int $productId
     * @return string
     */
    public function getPrice($productId)
    {
        return $this->_rootElement->find(
            '.price-box #product-price-' . $productId . ' .price',
            Locator::SELECTOR_CSS
        )->getText();
    }
}

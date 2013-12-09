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
    protected $clickForPrice = '[id*=msrp-click]';

    /**
     * Old (MAP) price
     *
     * @var string
     */
    protected $priceMap = '[id*=product-price]';

    /**
     * Actual product price
     *
     * @var string
     */
    protected $actualPrice = "[class='regular-price']";

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
    public function openMapBlockOnCategoryPage()
    {
        $this->_rootElement->find($this->clickForPrice, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Get Minimum Advertised Price value on frontend (Category page)
     *
     * @return array|string
     */
    public function getOldPrice()
    {
        return $this->_rootElement->find($this->priceMap, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Get actual Price value on frontend (Category page)
     *
     * @return array|string
     */
    public function getActualPrice()
    {
        return $this->_rootElement->find($this->actualPrice, Locator::SELECTOR_CSS)->getText();
    }
}

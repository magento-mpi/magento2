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
     * @var string
     */
    protected $clickForPrice = '[id*=msrp-click]';

    protected $priceMap = '[id*=product-price]';

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

    public function openMapBlockOnCategoryPage()
    {
        $this->_rootElement->find($this->clickForPrice, Locator::SELECTOR_CSS)->click();
    }

    public function getOldPrice()
    {
        return $this->_rootElement->find($this->priceMap, Locator::SELECTOR_CSS)->getText();
    }

    public function getActualPrice()
    {
        return $this->_rootElement->find($this->actualPrice, Locator::SELECTOR_CSS)->getText();
    }
}

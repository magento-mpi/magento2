<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class SearchResultsList
 * Product list
 */
class ListProduct extends Block
{
    /**
     * This member holds the class name of the regular price block.
     *
     * @var string
     */
    protected $regularPriceClass = ".regular-price";

    /**
     * This member holds the class name for the price block found inside the product details.
     *
     * @var string
     */
    protected $priceBlockClass = 'price-box';

    /**
     * This member contains the selector to find the product details for the named product.
     *
     * @var string
     */
    protected $productDetailsSelector = '//*[@class="product details" and .//*[@title="%s"]]';

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
    protected $clickForPrice = "//div[contains(@class, 'product details') and ('%s')]//a[contains(@id, 'msrp-popup')]";

    /**
     * Minimum Advertised Price on category page
     *
     * @var string
     */
    protected $oldPrice = ".old-price .price";

    /**
     * 'Add to Card' button
     *
     * @var string
     */
    protected $addToCard = "button.action.tocart";

    /**
     * This method returns the price box block for the named product.
     *
     * @param string $productName String containing the name of the product to find.
     * @return Price
     */
    public function getProductPriceBlock($productName)
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductPrice(
            $this->getProductDetailsElement($productName)->find($this->priceBlockClass, Locator::SELECTOR_CLASS_NAME)
        );
    }

    /**
     * Check if product with specified name is visible
     *
     * @param string $productName
     *
     * @return bool
     */
    public function isProductVisible($productName)
    {
        return $this->getProductNameElement($productName)->isVisible();
    }

    /**
     * Check if regular price is visible.
     *
     * @return bool
     */
    public function isRegularPriceVisible()
    {
        return $this->_rootElement->find($this->regularPriceClass)->isVisible();
    }

    /**
     * Open product view page by clicking on product name
     *
     * @param string $productName
     */
    public function openProductViewPage($productName)
    {
        $this->getProductNameElement($productName)->click();
    }

    /**
     * This method returns the element representing the product details for the named product.
     *
     * @param string $productName String containing the name of the product
     *
     * @return Element
     */
    protected function getProductDetailsElement($productName)
    {
        return $this->_rootElement->find(
            sprintf($this->productDetailsSelector, $productName),
            Locator::SELECTOR_XPATH
        );
    }

    /**
     * This method returns the element on the page associated with the product name.
     *
     * @param string $productName String containing the name of the product
     *
     * @return Element
     */
    protected function getProductNameElement($productName)
    {
        return $this->_rootElement->find(
            $this->productTitle,
            Locator::SELECTOR_CSS
        )->find(
            '//*[@title="' . $productName . '"]',
            Locator::SELECTOR_XPATH
        );
    }

    /**
     * Open MAP block on category page
     *
     * @param $productName
     *
     * @return void
     */
    public function openMapBlockOnCategoryPage($productName)
    {
        $this->_rootElement->find(sprintf($this->clickForPrice, $productName), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get Minimum Advertised Price on Category page
     *
     * @return string
     */
    public function getOldPriceCategoryPage()
    {
        return $this->_rootElement->find($this->oldPrice, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Retrieve product price by specified Id
     *
     * @param int $productId
     *
     * @return string
     */
    public function getPrice($productId)
    {
        return $this->_rootElement->find(
            '.price-box #product-price-' . $productId . ' .price',
            Locator::SELECTOR_CSS
        )->getText();
    }

    /**
     * Check 'Add To Card' button availability
     *
     * @return bool
     */
    public function checkAddToCardButton()
    {
        return $this->_rootElement->find($this->addToCard, Locator::SELECTOR_CSS)->isVisible();
    }
}

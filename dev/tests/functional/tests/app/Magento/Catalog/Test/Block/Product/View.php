<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Test\Block\Product;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Catalog\Test\Fixture\GroupedProduct;
use Magento\Bundle\Test\Fixture\Bundle as BundleFixture;

/**
 * Class View
 * Product View block
 *
 * @package Magento\Catalog\Test\Block\Product\View
 */
class View extends Block
{
    /**
     * 'Add to Cart' button
     *
     * @var string
     */
    protected $addToCart = '#product-addtocart-button';

    /**
     * 'Check out with PayPal' button
     *
     * @var string
     */
    protected $paypalCheckout = '[data-action=checkout-form-submit]';

    /**
     * This member holds the class name for the price block found inside the product details.
     *
     * @var string
     */
    protected $priceBlockClass = 'price-box';

    /**
     * Product name element
     *
     * @var string
     */
    protected $productName = '.page.title.product span';

    /**
     * Product price element
     *
     * @var string
     */
    protected $productPrice = '.price-box .price';

    /**
     * Bundle options block
     *
     * @var string
     */
    protected $bundleBlock = '#product-options-wrapper';

    /**
     * Click for Price link on Product page
     *
     * @var string
     */
    protected $clickForPrice = '[id*=msrp-popup]';

    /**
     * MAP popup on Product page
     *
     * @var string
     */
    protected $mapPopup = '#map-popup';

    /**
     * Stock Availability control
     *
     * @var string
     */
    protected $stockAvailability = '.stock span';

    /**
     * Get bundle options block
     *
     * @return \Magento\Bundle\Test\Block\Catalog\Product\View\Type\Bundle
     */
    protected function getBundleBlock()
    {
        return Factory::getBlockFactory()->getMagentoBundleCatalogProductViewTypeBundle(
            $this->_rootElement->find($this->bundleBlock)
        );
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\Price
     */
    protected function getPriceBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductPrice(
            $this->_rootElement->find('.product.info.main .price-box')
        );
    }

    /**
     * Add product to shopping cart
     *
     * @param Product $product
     */
    public function addToCart(Product $product)
    {
        $this->fillOptions($product);
        $this->_rootElement->find($this->addToCart, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Press 'Check out with PayPal' button
     */
    public function paypalCheckout()
    {
        $this->_rootElement->find($this->paypalCheckout, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Get product name displayed on page
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->_rootElement->find($this->productName, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * This method returns the price box block.
     *
     * @return Price
     */
    public function getProductPriceBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductPrice(
            $this->_rootElement->find($this->priceBlockClass, Locator::SELECTOR_CLASS_NAME)
        );
    }

    /**
     * Return product price displayed on page
     *
     * @return array|string Returns arrays with keys corresponding to fixture keys
     */
    public function getProductPrice()
    {
        return $this->getPriceBlock()->getPrice();
    }

    /**
     * Return configurable product options
     *
     * @return array
     */
    public function getProductOptions()
    {
        $options = array();
        for ($i = 2; $i <= 3; $i++) {
            $options[] = $this->_rootElement->find(".super-attribute-select option:nth-child({$i})")->getText();
        }
        return $options;
    }

    /**
     * Verify configurable product options
     *
     * @param ConfigurableProduct $product
     * @return bool
     */
    public function verifyProductOptions(ConfigurableProduct $product)
    {
        $attributes = $product->getConfigurableOptions();
        foreach ($attributes as $attributeName => $attribute) {
            foreach ($attribute as $optionName) {
                $option = $this->_rootElement->find(
                    '//*[*[@class="product options configure"]//span[text()="' .
                    $attributeName .
                    '"]]//select/option[contains(text(), "' .
                    $optionName .
                    '")]',
                    Locator::SELECTOR_XPATH
                );
                if (!$option->isVisible()) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Fill in the option specified for the product
     *
     * @param BundleFixture|Product $product
     */
    public function fillOptions($product)
    {
        $configureButton = $this->_rootElement->find('.action.primary.customize');
        $configureSection = $this->_rootElement->find('.product.options.configure');

        if ($configureButton->isVisible()) {
            $configureButton->click();
            $bundleOptions = $product->getSelectionData();
            $this->getBundleBlock()->fillBundleOptions($bundleOptions);
        }
        if ($configureSection->isVisible()) {
            $productOptions = $product->getProductOptions();
            $this->getBundleBlock()->fillProductOptions($productOptions);
        }
    }

    /**
     * Click "ADD TO CART" button
     */
    public function clickAddToCartButton()
    {
        $this->_rootElement->find($this->addToCart, Locator::SELECTOR_CSS)->click();
    }

    /**
     * @param GroupedProduct $product
     * @return bool
     */
    public function verifyGroupedProducts(GroupedProduct $product)
    {
        foreach ($product->getAssociatedProductNames() as $name) {
            $option = $this->_rootElement->find(
                "//*[@id='super-product-table']//tr[td/strong='{$name}']",
                Locator::SELECTOR_XPATH
            );
            if (!$option->isVisible()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Open MAP block on Product View page
     */
    public function openMapBlockOnProductPage()
    {
        $this->_rootElement->find($this->clickForPrice, Locator::SELECTOR_CSS)->click();
        $this->waitForElementVisible($this->mapPopup, Locator::SELECTOR_CSS);
    }

    /**
     * Is 'ADD TO CART' button visible
     *
     * @return bool
     */
    public function isAddToCartButtonVisible()
    {
        return $this->_rootElement->find($this->addToCart, Locator::SELECTOR_CSS)->isVisible();
    }

    /**
     * Get text of Stock Availability control
     *
     * @return array|string
     */
    public function stockAvailability()
    {
        return $this->_rootElement->find($this->stockAvailability)->getText();
    }
}

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
use Magento\Bundle\Test\Block\Catalog\Product\View\Type\Bundle;

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
    private $addToCart;

    /**
     * 'Check out with PayPal' button
     *
     * @var string
     */
    private $paypalCheckout;

    /**
     * Product name element
     *
     * @var string
     */
    private $productName;

    /**
     * Product price element
     *
     * @var string
     */
    private $productPrice;

    /**
     * Bundle options block
     *
     * @var Bundle
     */
    private $bundleBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        //Elements
        $this->addToCart = '#product-addtocart-button';
        $this->paypalCheckout = '[data-action=checkout-form-submit]';
        $this->productName = '.page.title.product span';
        $this->productPrice = '.price-box .price';

        //Blocks
        $this->bundleBlock = Factory::getBlockFactory()->getMagentoBundleCatalogProductViewTypeBundle(
            $this->_rootElement->find('#product-options-wrapper', Locator::SELECTOR_CSS));
    }

    /**
     * Get bundle options block
     *
     * @return \Magento\Bundle\Test\Block\Catalog\Product\View\Type\Bundle
     */
    public function getBundleBlock()
    {
        return $this->bundleBlock;
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
     * Return product price displayed on page
     *
     * @return array|string
     */
    protected function _getSimplePrice()
    {
        return $this->_rootElement->find($this->productPrice)->getText();
    }

    /**
     * Return product price displayed on page
     *
     * @return array|string Returns arrays with keys corresponding to fixture keys
     */
    public function getProductPrice()
    {
        $priceFromTo = $this->_getPriceFromTo();
        return empty($priceFromTo) ? $this->_getSimplePrice() : $priceFromTo;
    }

    /**
     * Get bundle product price in form "From: To:"
     *
     * @return array F.e. array('price_from' => '$110', 'price_to' => '$120')
     */
    protected function _getPriceFromTo()
    {
        $priceFrom = $this->_rootElement->find('.price-from');
        $priceTo = $this->_rootElement->find('.price-to');
        $price = array();
        if ($priceFrom->isVisible()) {
            $price['price_from'] = $priceFrom->find('.price')->getText();
        }
        if ($priceTo->isVisible()) {
            $price['price_to'] = $priceTo->find('.price')->getText();
        }
        return $price;
    }

    /**
     * Return configurable product options
     *
     * @return array
     */
    public function getProductOptions()
    {
        for ($i =2; $i<=3; $i++) {
            $options[] = $this->_rootElement
                ->find(".super-attribute-select option:nth-child($i)")->getText();
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
                $option = $this->_rootElement->find('//*[*[@class="product options configure"]//span[text()="'
                    . $attributeName . '"]]//select/option[contains(text(), "' . $optionName . '")]',
                    Locator::SELECTOR_XPATH);
                if (!$option->isVisible()) {
                    return false;
                };
            }
        }
        return true;
    }

    /**
     * Fill in the option specified for the product
     *
     * @param Product $product
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
}

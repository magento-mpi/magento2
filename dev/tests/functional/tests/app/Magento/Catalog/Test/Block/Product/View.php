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
use \Magento\Bundle\Test\Block\Catalog\Product\View\Type\Bundle;

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
        if ($this->getBundleBlock()->isVisible()) {
            $this->getBundleBlock()->fillBundleOptions($product);
        }
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
     * @return array|string
     */
    public function getProductName()
    {
        return $this->_rootElement->find('//*[@class="page title product"]//span', Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Return product price displayed on page
     *
     * @return array|string
     */
    protected function _getSimplePrice()
    {
        return $this->_rootElement->find('.price-box .price')->getText();
    }

    /**
     * Return product price displayed on page
     *
     * @return array Returns arrays with keys corresponding to fixture keys
     */
    public function getProductPrice()
    {
        $priceFromTo = $this->_getPriceFromTo();
        return empty($priceFromTo) ? array('price' => $this->_getSimplePrice()) : $priceFromTo;
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
}

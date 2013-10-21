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
 * @package Magento\Catalog\Test\Block\Product
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
    public function getProductPrice()
    {
        return $this->_rootElement
            ->find('//*[@class="price-box"]//span[@class="price"]', Locator::SELECTOR_XPATH)
            ->getText();
    }
}

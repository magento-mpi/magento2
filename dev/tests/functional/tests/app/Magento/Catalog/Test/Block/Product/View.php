<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class View
 * Product view block on the product page
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class View extends Block
{
    /**
     * XPath selector for tab
     *
     * @var string
     */
    protected $tabSelector = './/div[@data-role="collapsible" and a[contains(text(),"%s")]]';

    /**
     * Custom options CSS selector
     *
     * @var string
     */
    protected $customOptionsSelector = '.product-options-wrapper';

    /**
     * 'Add to Cart' button
     *
     * @var string
     */
    protected $addToCart = '.tocart';

    /**
     * Quantity input id
     *
     * @var string
     */
    protected $qty = '#qty';

    /**
     * 'Check out with PayPal' button
     *
     * @var string
     */
    protected $paypalCheckout = '[data-action=checkout-form-submit]';

    /**
     * Product name element
     *
     * @var string
     */
    protected $productName = '.page-title.product h1.title .base';

    /**
     * Product sku element
     *
     * @var string
     */
    protected $productSku = '[itemprop="sku"]';

    /**
     * Product description element
     *
     * @var string
     */
    protected $productDescription = '.product.attibute.description';

    /**
     * Product short-description element
     *
     * @var string
     */
    protected $productShortDescription = '.product.attibute.overview';

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
    protected $mapPopup = '#map-popup-click-for-price';

    /**
     * Stock Availability control
     *
     * @var string
     */
    protected $stockAvailability = '.stock span';

    /**
     * Customize and add to cart button selector
     *
     * @var string
     */
    protected $customizeButton = '.action.primary.customize';

    /**
     * This member holds the class name of the tier price block.
     *
     * @var string
     */
    protected $tierPricesSelector = "//ul[contains(@class,'tier')]//*[@class='item'][%line-number%]";

    /**
     * Selector for price block
     *
     * @var string
     */
    protected $priceBlock = '//*[@class="product-info-main"]//*[contains(@class,"price-box")]';

    /**
     * 'Add to Compare' button
     *
     * @var string
     */
    protected $clickAddToCompare = '.action.tocompare';

    /**
     * "Add to Wishlist" button
     *
     * @var string
     */
    protected $addToWishlist = '[data-action="add-to-wishlist"]';

    /**
     * Widgets selectors
     *
     * @var array
     */
    protected $widgetSelectors = [
        'Banner Rotator' => './/div[contains(@class,"widget banners") and contains(.,"%s")]',
        'CMS Page Link' => './/div[contains(@class,"widget widget-cms-link") and contains(text(),"%s")]',
        'Catalog Category Link' => './/*/a[contains(.,"%s")]',
        'Catalog Product Link' => './/*/a[contains(.,"%s")]',
        'Order by SKU' => './/*/a[contains(.,"%s")]',
        'Recently Compared Products' => './/div[contains(@class,"block compare") and contains(text(),"%s")]',
        'Recently Viewed Products' => './/div[contains(@class,"block viewed links") and contains(text(),"%s")]',
        'Catalog New Products List' => './/div[contains(@class,"widget new") and contains(text(),"%s")]',
        'CMS Static Block' => './/div[contains(@class,"widget static block") and contains(text(),"%s")]'
    ];

    /**
     * Check is visible widget selector
     *
     * @param string $widgetType
     * @param string $widgetText
     * @return bool
     * @throws \Exception
     */
    public function isWidgetVisible($widgetType, $widgetText)
    {
        if (isset($this->widgetSelectors[$widgetType])) {
            return $this->_rootElement->find(
                sprintf($this->widgetSelectors[$widgetType], $widgetText),
                Locator::SELECTOR_XPATH
            )->isVisible();
        } else {
            throw new \Exception('Determine how to find the widget on the page.');
        }
    }

    /**
     * Click to widget selector
     *
     * @param string $widgetType
     * @param string $widgetText
     * @return bool
     * @throws \Exception
     */
    public function clickToWidget($widgetType, $widgetText)
    {
        if (isset($this->widgetSelectors[$widgetType])) {
            $this->_rootElement->find(
                sprintf($this->widgetSelectors[$widgetType], $widgetText),
                Locator::SELECTOR_XPATH
            )->click();
        } else {
            throw new \Exception('Determine how to find the widget on the page.');
        }
    }

    /**
     * Get bundle options block
     *
     * @return \Magento\Bundle\Test\Block\Catalog\Product\View\Type\Bundle
     */
    public function getBundleBlock()
    {
        return Factory::getBlockFactory()->getMagentoBundleCatalogProductViewTypeBundle(
            $this->_rootElement->find($this->bundleBlock)
        );
    }

    /**
     * This method returns the custom options block.
     *
     * @return \Magento\Catalog\Test\Block\Product\View\CustomOptions
     */
    public function getCustomOptionsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Catalog\Test\Block\Product\View\CustomOptions',
            ['element' => $this->_rootElement->find($this->customOptionsSelector)]
        );
    }

    /**
     * Add product to shopping cart
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function addToCart(FixtureInterface $product)
    {
        /** @var CatalogProductSimple $product */
        $checkoutData = $product->getCheckoutData();

        $this->fillOptions($product);
        if (isset($checkoutData['qty'])) {
            $this->_rootElement->find($this->qty)->setValue($checkoutData['qty']);
        }
        $this->clickAddToCart();
    }

    /**
     * Click link
     *
     * @return void
     */
    public function clickAddToCart()
    {
        $this->_rootElement->find($this->addToCart, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Set quantity and click add to cart
     *
     * @param int $qty
     * @return void
     */
    public function setQtyAndClickAddToCart($qty)
    {
        $this->_rootElement->find($this->qty, Locator::SELECTOR_CSS)->setValue($qty);
        $this->clickAddToCart();
    }

    /**
     * Find Add To Cart button
     *
     * @return bool
     */
    public function isVisibleAddToCart()
    {
        return $this->_rootElement->find($this->addToCart, Locator::SELECTOR_CSS)->isVisible();
    }

    /**
     * Press 'Check out with PayPal' button
     *
     * @return void
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
     * Get product sku displayed on page
     *
     * @return string
     */
    public function getProductSku()
    {
        return $this->_rootElement->find($this->productSku, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Return product short description on page
     *
     * @return string|null
     */
    public function getProductShortDescription()
    {
        if ($this->_rootElement->find($this->productShortDescription, Locator::SELECTOR_CSS)->isVisible()) {
            return $this->_rootElement->find($this->productShortDescription, Locator::SELECTOR_CSS)->getText();
        }
        return null;
    }

    /**
     * Return product description on page
     *
     * @return string|null
     */
    public function getProductDescription()
    {
        if ($this->_rootElement->find($this->productDescription, Locator::SELECTOR_CSS)->isVisible()) {
            return $this->_rootElement->find($this->productDescription, Locator::SELECTOR_CSS)->getText();
        }
        return null;
    }

    /**
     * Return product options
     *
     * @param FixtureInterface $product
     * @return array
     */
    public function getOptions(FixtureInterface $product)
    {
        /** @var CatalogProductSimple $product */
        $dataConfig = $product->getDataConfig();
        $typeId = isset($dataConfig['type_id']) ? $dataConfig['type_id'] : null;

        return $this->hasRender($typeId)
            ? $this->callRender($typeId, 'getOptions', ['product' => $product])
            : $this->getCustomOptionsBlock()->getOptions($product);
    }

    /**
     * Fill in the option specified for the product
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function fillOptions(FixtureInterface $product)
    {
        $dataConfig = $product->getDataConfig();
        $typeId = isset($dataConfig['type_id']) ? $dataConfig['type_id'] : null;
        $checkoutData = null;

        /** @var CatalogProductSimple $product */
        if ($this->hasRender($typeId)) {
            $this->callRender($typeId, 'fillOptions', ['product' => $product]);
        } else {
            $checkoutCustomOptions = [];

            if ($product instanceof InjectableFixture) {
                /** @var CatalogProductSimple $product */
                $checkoutData = $product->getCheckoutData();
                $checkoutCustomOptions = isset($checkoutData['options']['custom_options'])
                    ? $checkoutData['options']['custom_options']
                    : [];
                $customOptions = $product->hasData('custom_options')
                    ? $product->getDataFieldConfig('custom_options')['source']->getCustomOptions()
                    : [];

                $checkoutCustomOptions = $this->prepareCheckoutData($customOptions, $checkoutCustomOptions);
            }

            $this->getCustomOptionsBlock()->fillCustomOptions($checkoutCustomOptions);
        }

        if (isset($checkoutData['options']['qty'])) {
            $this->_rootElement->find($this->qty)->setValue($checkoutData['options']['qty']);
        }
    }

    /**
     * Replace index fields to name fields in checkout data
     *
     * @param array $options
     * @param array $checkoutData
     * @return array
     */
    protected function prepareCheckoutData(array $options, array $checkoutData)
    {
        $result = [];

        foreach ($checkoutData as $checkoutOption) {
            $attribute = str_replace('attribute_key_', '', $checkoutOption['title']);
            $option = str_replace('option_key_', '', $checkoutOption['value']);

            if (isset($options[$attribute])) {
                $result[] = [
                    'type' => strtolower(preg_replace('/[^a-z]/i', '', $options[$attribute]['type'])),
                    'title' => isset($options[$attribute]['title'])
                            ? $options[$attribute]['title']
                            : $attribute,
                    'value' => isset($options[$attribute]['options'][$option]['title'])
                            ? $options[$attribute]['options'][$option]['title']
                            : $option
                ];
            }
        }

        return $result;
    }

    /**
     * This method return array tier prices
     *
     * @param int $lineNumber [optional]
     * @return array
     */
    public function getTierPrices($lineNumber = 1)
    {
        return $this->_rootElement->find(
            str_replace('%line-number%', $lineNumber, $this->tierPricesSelector),
            Locator::SELECTOR_XPATH
        )->getText();
    }

    /**
     * Click "ADD TO CART" button
     *
     * @return void
     */
    public function clickAddToCartButton()
    {
        $this->_rootElement->find($this->addToCart, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Open MAP block on Product View page
     *
     * @return void
     */
    public function openMapBlockOnProductPage()
    {
        $this->_rootElement->find($this->clickForPrice, Locator::SELECTOR_CSS)->click();
        $this->waitForElementVisible($this->mapPopup, Locator::SELECTOR_CSS);
    }

    /**
     * Check 'Add to card' button visible
     *
     * @return bool
     */
    public function checkAddToCardButton()
    {
        return $this->_rootElement->find($this->addToCart, Locator::SELECTOR_CSS)->isVisible();
    }

    /**
     * Get text of Stock Availability control
     *
     * @return string
     */
    public function stockAvailability()
    {
        return strtolower($this->_rootElement->find($this->stockAvailability)->getText());
    }

    /**
     * Click "Add to Compare" button
     *
     * @return void
     */
    public function clickAddToCompare()
    {
        $this->_rootElement->find($this->clickAddToCompare, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Click "Add to Wishlist" button
     *
     * @return void
     */
    public function addToWishlist()
    {
        $this->_rootElement->find($this->addToWishlist, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Select tab on the product page
     *
     * @param string $name
     * @return void
     */
    public function selectTab($name)
    {
        $this->_rootElement->find(sprintf($this->tabSelector, $name), Locator::SELECTOR_XPATH)->click();
    }
}

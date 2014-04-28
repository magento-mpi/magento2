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

use Magento\Connect\Controller\Adminhtml\Extension\Local;
use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Catalog\Test\Fixture\GroupedProduct;
use Magento\Bundle\Test\Fixture\Bundle as BundleFixture;
use Mtf\Fixture\FixtureInterface;

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
     * Product sku element
     *
     * @var string
     */
    protected $productSku = "div[itemprop='sku']";

    /**
     * Product sku element
     *
     * @var string
     */
    protected $productDescription = "//*/dd/div[contains(concat(' ', @class, ' '), ' description')]/div[@class='value']";

    /**
     * Product sku element
     *
     * @var string
     */
    protected $productShortDescription = "div[itemprop='description']";

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
     * Customize and add to cart button selector
     *
     * @var string
     */
    protected $customizeButton = '.action.primary.customize';

    /**
     * downloadLinksData
     *
     * @var string
     */
    protected $downloadLinksData = '.downloads';
    protected $downloadLinksDataTitleForForLink = ".//*/label/span[text()='";
    protected $downloadLinksDataTitleForList = "//*[@id='downloadable-links-list']/div[%d]/label[@class='label']/span[text() = '";
    protected $downloadLinksDataPriceForList = "//*[@id='downloadable-links-list']/div[@data-role='link']/label/span[@class='price-notice']/span[text() = '";
    protected $downloadLinksDataCheckboxForList = "//*[@id='downloadable-links-list']/div[%d]/input[@type='checkbox']";
    protected $downloadLinksDataItemBlock = '[data-role=link]';

    /**
     * downloadSamplesData
     *
     * @var string
     */
    protected $downloadSamplesDataTitleForForSample = "//*/dl[contains(concat(' ', @class, ' '), ' downloadable')]/dt[contains(concat(' ', @class, ' '), ' title')][text()='";
    protected $downloadSampleDataTitleForList = "//*/dl[contains(concat(' ', @class, ' '), ' downloadable')]/dd[%d]/a[text()[contains(., '";

    /**
     * Verify DownloadableLinksData
     *
     * @param FixtureInterface $product
     * @return bool
     */
    public function downloadLinksData(FixtureInterface $product)
    {
        $dBlock = $this->_rootElement->find($this->stockAvailability);
        $fields = $product->getData();
        //Steps:
        //1. Title for for Link block
        if (!$dBlock->find(
            $this->downloadLinksDataTitleForForLink . $fields['downloadable_links']['title'] . "']",
            Locator::SELECTOR_XPATH
        )
        ) {
            return false;
        }
        if (isset($fields['downloadable_links'])) {
            foreach ($fields['downloadable_links']['downloadable']['link'] as $index => $link) {
                //2. Titles for each links
                //6. Links are displaying according to Sort Order
                $formatTitle = sprintf($this->downloadLinksDataTitleForList . $link['title'] . "']", ($index + 1));
                if (!$dBlock->find($formatTitle, Locator::SELECTOR_XPATH)) {
                    return false;
                }
                //3. If Links can be Purchase Separately, check-nob is presented near each link
                //4. If Links CANNOT be Purchase Separately, check-nob is not presented near each link
                $formatPrice = sprintf($this->downloadLinksDataCheckboxForList, ($index + 1));
                if ($fields['downloadable_links']['links_purchased_separately'] == "Yes") {
                    if (!$dBlock->find($formatPrice, Locator::SELECTOR_XPATH)) {
                        return false;
                    }
                } elseif ($fields['downloadable_links']['links_purchased_separately'] == "No") {
                    if ($dBlock->find($formatPrice, Locator::SELECTOR_XPATH)) {
                        return false;
                    }
                }
                //5. Price is equals passed according to fixture
                $formatPrice = sprintf($this->downloadLinksDataPriceForList . '$%1.2f' . "']", $link['price']);
                if (!$dBlock->find($formatPrice, Locator::SELECTOR_XPATH)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Verify DownloadableSamplesData
     *
     * @param FixtureInterface $product
     * @return bool
     */
    public function downloadSamplesData(FixtureInterface $product)
    {
        $dBlock = $this->_rootElement->find($this->stockAvailability);
        $fields = $product->getData();
        //Steps:
        //1. Title for for sample block
        if (!$dBlock->find(
            $this->downloadSamplesDataTitleForForSample . $fields['downloadable_sample']['title'] . "']",
            Locator::SELECTOR_XPATH
        )
        ) {
            return false;
        }
        if (isset($fields['downloadable_sample'])) {
            foreach ($fields['downloadable_sample']['downloadable']['sample'] as $index => $sample) {
                //2. Titles for each sample
                //3. Samples are displaying according to Sort Order
                $formatTitle = sprintf($this->downloadSampleDataTitleForList . $sample['title'] . "')]]", ($index + 1));
                if (!$dBlock->find($formatTitle, Locator::SELECTOR_XPATH)) {
                    return false;
                }
            }
        }
        return true;
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
     * @param FixtureInterface $product
     */
    public function addToCart(FixtureInterface $product)
    {
        $this->fillOptions($product);
        $this->clickAddToCart();
    }

    /**
     * Click link
     */
    public function clickAddToCart()
    {
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
     * Get product sku displayed on page
     *
     * @return string
     */
    public function getProductSku()
    {
        return $this->_rootElement->find($this->productSku, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Get product description displayed on page
     *
     * @return string
     */
    public function getProductDescription()
    {
        if ($productDescription = $this->_rootElement->find($this->productDescription, Locator::SELECTOR_XPATH)) {
            return $productDescription->getText();
        } else {
            return false;
        }
    }

    /**
     * Get product short description displayed on page
     *
     * @return string
     */
    public function getProductShortDescription()
    {
        if ($productShortDescription = $this->_rootElement->find(
            $this->productShortDescription,
            Locator::SELECTOR_CSS
        )
        ) {
            return $productShortDescription->getText();
        } else {
            return false;
        }
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
                    '//*[*[@class="field configurable required"]//span[text()="' .
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
        $configureButton = $this->_rootElement->find($this->customizeButton);
        $configureSection = $this->_rootElement->find('.product.options.wrapper');

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
     * Click "Customize and add to cart button"
     */
    public function clickCustomize()
    {
        $this->_rootElement->find($this->customizeButton)->click();

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

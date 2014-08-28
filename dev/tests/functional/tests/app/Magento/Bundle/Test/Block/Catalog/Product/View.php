<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Catalog\Product;

use Mtf\Client\Element\Locator;
use Magento\Bundle\Test\Block\Catalog\Product\View\Type\Bundle;
use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Magento\Bundle\Test\Fixture\CatalogProductBundle;

/**
 * Class View
 * Bundle product view block on the product page
 */
class View extends \Magento\Catalog\Test\Block\Product\View
{
    /**
     * Customize and add to cart button selector
     *
     * @var string
     */
    protected $customizeButton = '.action.primary.customize';

    /**
     * Bundle options block
     *
     * @var string
     */
    protected $bundleBlock = '//*[@id="product-options-wrapper"]//fieldset[contains(@class,"bundle")]';

    /**
     * Get bundle options block
     *
     * @return Bundle
     */
    public function getBundleBlock()
    {
        return $this->blockFactory->create(
            'Magento\Bundle\Test\Block\Catalog\Product\View\Type\Bundle',
            ['element' => $this->_rootElement->find($this->bundleBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Click "Customize and add to cart button"
     *
     * @return void
     */
    public function clickCustomize()
    {
        $this->_rootElement->find($this->customizeButton)->click();
        $this->waitForElementVisible($this->addToCart);
    }

    /**
     * Return product options
     *
     * @param FixtureInterface $product [optional]
     * @return array
     */
    public function getOptions(FixtureInterface $product = null)
    {
        $options = [];

        $this->clickCustomize();
        $options['bundle_options'] = $this->getBundleBlock()->getOptions($product);
        $options += parent::getOptions($product);

        return $options;
    }

    /**
     * Fill in the option specified for the product
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function fillOptions(FixtureInterface $product)
    {
        $bundleCheckoutData = [];

        if ($product instanceof InjectableFixture) {
            /** @var \Magento\Bundle\Test\Fixture\CatalogProductBundle $product */
            $checkoutData = $product->getCheckoutData();
            $bundleCheckoutData = isset($checkoutData['bundle_options']) ? $checkoutData['bundle_options'] : [];
        } else {
            /** @var \Magento\Bundle\Test\Fixture\BundleFixed $product */
            $bundleCheckoutData = $product->getSelectionData();
        }
        $this->_rootElement->find($this->customizeButton)->click();
        $this->getBundleBlock()->fillBundleOptions($bundleCheckoutData);

        parent::fillOptions($product);
    }
}

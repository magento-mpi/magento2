<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma\RmaNew\Tab\Items\Order;

use Mtf\Client\Element\Locator;
use Magento\Rma\Test\Block\Adminhtml\Product\Bundle\Items as BundleItems;
use Mtf\Fixture\FixtureInterface;
use Magento\Bundle\Test\Fixture\BundleProduct;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class BundleGrid
 * Grid for choose order item(bundle product).
 */
class BundleGrid extends Grid
{
    /**
     * Popup block for choose items of returned bundle product.
     *
     * @var string
     */
    protected $bundleItemsPopup = '//ancestor::div//div[@id="details_container"]';

    /**
     * Select order item.
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function selectItem(FixtureInterface $product)
    {
        /** @var BundleProduct $product */
        $checkoutData = $product->getCheckoutData();
        $bundleOptions = isset($checkoutData['options']['bundle_options'])
            ? $checkoutData['options']['bundle_options']
            : [];
        $labels = [];

        foreach ($bundleOptions as $option) {
            $labels[] = $option['value']['name'];
        }

        $this->searchAndSelect(['sku' => $product->getSku()]);
        /** @var BundleItems $bundleItems */
        $bundleItems = $this->blockFactory->create(
            'Magento\Rma\Test\Block\Adminhtml\Product\Bundle\Items',
            ['element' => $this->_rootElement->find($this->bundleItemsPopup, Locator::SELECTOR_XPATH)]
        );
        $bundleItems->fill($labels);
    }
}

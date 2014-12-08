<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma\NewRma\Tab\Items\Order;

use Magento\Bundle\Test\Fixture\BundleProduct;
use Magento\Rma\Test\Block\Adminhtml\Product\Bundle\Items as BundleItems;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
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
        $this->getSelectItemsBlock()->fill($labels);
    }

    /**
     * Return popup select bundle items block.
     *
     * @return BundleItems
     */
    protected function getSelectItemsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Rma\Test\Block\Adminhtml\Product\Bundle\Items',
            ['element' => $this->_rootElement->find($this->bundleItemsPopup, Locator::SELECTOR_XPATH)]
        );
    }
}

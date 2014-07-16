<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Constraint\AssertProductGroupedPriceOnProductPage;

/**
 * Class AssertGroupedPriceOnBundleProductPage
 */
class AssertGroupedPriceOnBundleProductPage extends AssertProductGroupedPriceOnProductPage
{
    /**
     * Get grouped price with fixture product and product page
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @param string $customerGroup
     * @return array
     */
    protected function getGroupedPrice(
        CatalogProductView $catalogProductView,
        FixtureInterface $product,
        $customerGroup = 'NOT LOGGED IN'
    ) {
        $groupPrice['onPage'] = $catalogProductView->getViewBlock()->getProductPrice();
        $groupPrice['onPage'] = isset($groupPrice['onPage']['price_regular_price'])
            ? str_replace('As low as $', '', $groupPrice['onPage']['price_regular_price'])
            : str_replace('$', '', $groupPrice['onPage']['price_from']);
        $groupPrice['fixture'] = $product->getDataFieldConfig('price')['source']->getPreset()['price_from'];

        return $groupPrice;
    }
}

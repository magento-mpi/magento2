<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Mtf\Fixture\InjectableFixture;
use Mtf\Constraint\AbstractConstraint;
use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Bundle\Test\Page\Product\CatalogProductView;

/**
 * Class AssertBundleItemsOnProductPage
 * Assert that displayed product bundle items data on product page equals passed from fixture preset
 */
class AssertBundleItemsOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Bundle options block
     *
     * @var string
     */
    protected $bundleBlock = '#product-options-wrapper';

    /**
     * Assert that displayed product bundle items data on product page equals passed from fixture preset
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductBundle $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, CatalogProductBundle $product)
    {
        $catalogProductView->init($product);
        $catalogProductView->open();
        $catalogProductView->getViewBlock()->clickCustomize();
        $result = $this->displayedBundleBlock($catalogProductView, $product);
        \PHPUnit_Framework_Assert::assertTrue($result, $result);
    }

    /**
     * Displayed bundle block on frontend with correct fixture product
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductBundle $product
     * @return bool|string
     */
    protected function displayedBundleBlock(CatalogProductView $catalogProductView, CatalogProductBundle $product)
    {
        $fields = $product->getData();
        $bundleSelections = $fields['bundle_selections'];
        if (!isset($bundleSelections['preset'])) {
            return 'Bundle items data on product page is not equals to fixture preset.';
        }
        $preset = $bundleSelections['preset'];
        $products = $bundleSelections['products'];
        $index = 1;
        $catalogProductView->getViewBlock()->clickCustomize();
        foreach ($preset as $item) {
            $search_data['title'] = $item['title'];
            $search_data['type'] = $item['type'];
            $search_data['required'] = $item['required'];
            foreach ($item['assigned_products'] as $productIncrement => $selection) {
                if (!isset($products[$productIncrement])) {
                    break;
                }
                /** @var InjectableFixture $fixture */
                $fixture = $products[$productIncrement];
                $search_data['items']['product_' . $productIncrement]['id'] = $fixture->getData('id');
                $search_data['items']['product_' . $productIncrement]['name'] = $fixture->getData('name');
                $search_data['items']['product_' . $productIncrement]['price'] = $fixture->getData('price');
            }

            $result = $catalogProductView->getBundleViewBlock()->getBundleBlock()->displayedBundleItemOption(
                $search_data,
                $index++
            );
            if ($result !== true) {
                return $result;
            }
        }
        return true;
    }

    /**
     * Return Text if displayed on frontend equals with fixture
     *
     * @return string
     */
    public function toString()
    {
        return 'Bundle items data on product page equals to passed from fixture preset.';
    }
}
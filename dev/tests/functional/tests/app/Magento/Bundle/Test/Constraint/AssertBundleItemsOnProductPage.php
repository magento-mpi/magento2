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
        \PHPUnit_Framework_Assert::assertTrue($result['displayed'], $result['errorMessage']);
    }

    /**
     * Displayed bundle block on frontend with correct fixture product
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductBundle $product
     * @return array
     */
    protected function displayedBundleBlock(CatalogProductView $catalogProductView, CatalogProductBundle $product)
    {
        $fields = $product->getData();
        $bundleOptions = $fields['bundle_selections']['bundle_options'];
        if (!isset($bundleOptions)) {
            return [
                'displayed' => false,
                'errorMessage' => 'Bundle options data on product page is not equals to fixture preset.'
            ];
        }

        $catalogProductView->getViewBlock()->clickCustomize();
        foreach ($bundleOptions as $index => $item) {
            foreach ($item['assigned_products'] as &$selection) {
                $selection = $selection['search_data'];
            }
            $result = $catalogProductView->getBundleViewBlock()->getBundleBlock()->displayedBundleItemOption(
                $item,
                ++$index
            );

            if ($result !== true) {
                return [
                    'displayed' => false,
                    'errorMessage' => 'Bundle item option "' . $item['assigned_products']['name']
                        . '" data on product page is not equals to fixture preset.'
                ];
            }
        }
        return [
            'displayed' => true,
            'errorMessage' => ''
        ];
    }

    /**
     * Return Text if displayed on frontend equals with fixture
     *
     * @return string
     */
    public function toString()
    {
        return 'Bundle options data on product page equals to passed from fixture preset.';
    }
}
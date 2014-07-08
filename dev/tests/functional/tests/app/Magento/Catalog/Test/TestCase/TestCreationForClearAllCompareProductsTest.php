<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Config\Test\Fixture\Config;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Test creation for Clear All CompareProducts
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Products from products/dataSet is created
 *       simple_for_composite_products
 *       virtual_product
 *       gift_card_product
 *       downloadable_product
 *       grouped_product
 *       configurable_product
 *       bundle_dynamic_product
 *       bundle_fixed_product
 * 2. Default customer is created
 *
 * Steps:
 * 1. Login to frontend.
 * 2. Add to Compare Product $products (as flow can be used MTA-54)
 * 3. Navigate to My Account page
 * 4. Click "Clear All" icon under the left menu tabs
 * 5. Perform assertions
 *
 * @group Compare_Products_(MX)
 * @ZephyrId MAGETWO-25961
 */
class TestCreationForClearAllCompareProductsTest extends Injectable
{
    /**
     * Test creation for clear all compare products
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerAccountLogin $customerAccountLogin
     * @param $products
     * @param CustomerInjectable $customer
     * @param CatalogProductView $catalogProductView
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param Config $config
     * @return void
     */
    public function test(
        FixtureFactory $fixtureFactory,
        CustomerAccountLogin $customerAccountLogin,
        $products,
        CustomerInjectable $customer,
        CatalogProductView $catalogProductView,
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        Config $config
    ) {
        $config->persist();

        $customer->persist();
        $cmsIndex->open();
        if (!$cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $cmsIndex->getLinksBlock()->openLink("Log In");
            $customerAccountLogin->getLoginBlock()->login($customer);
        }

        $products = explode(',', $products);
        foreach ($products as &$product) {
            list($fixture, $dataSet) = explode('::', $product);
            $product = $fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            $product->persist();
        }
        foreach ($products as $itemProduct) {
            $catalogProductView->init($itemProduct);
            $catalogProductView->open();
            $catalogProductView->getViewBlock()->clickAddToCompare();
        }

        $cmsIndex->getLinksBlock()->openLink("My Account");
        $customerAccountIndex->getCompareProductsBlock()->clickClearAll();
    }
}

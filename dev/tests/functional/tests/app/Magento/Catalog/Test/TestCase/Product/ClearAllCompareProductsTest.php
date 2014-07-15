<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Core\Test\Fixture\ConfigData;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Test creation for Clear All CompareProducts
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. All product types are created
 * 2. Customer created
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
class ClearAllCompareProductsTest extends AbstractCompareProductsTest
{
    /**
     * Test creation for clear all compare products
     *
     * @param string $products
     * @param ConfigData $config
     * @param CustomerAccountIndex $customerAccountIndex
     * @return void
     */
    public function test($products, ConfigData $config, CustomerAccountIndex $customerAccountIndex)
    {
        $config->persist();
        //Steps
        $this->cmsIndex->open();
        $this->loginCustomer($this->customer);

        $products = $this->createProducts($products);
        $this->addProducts($products);

        $this->cmsIndex->getLinksBlock()->openLink("My Account");
        $customerAccountIndex->getCompareProductsBlock()->clickClearAll();
    }
}

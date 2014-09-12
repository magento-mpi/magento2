<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Magento\Core\Test\Fixture\ConfigData;
use Magento\Customer\Test\Page\CustomerAccountIndex;

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
 * 2. Add to Compare Product $products
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
        $this->markTestIncomplete('MAGETWO-26865');
        // Preconditions
        $config->persist();
        $products = $this->createProducts($products);

        //Steps
        $this->cmsIndex->open();
        $this->loginCustomer($this->customer);
        $this->addProducts($products);
        $this->cmsIndex->getLinksBlock()->openLink("My Account");
        $customerAccountIndex->getCompareProductsBlock()->clickClearAll();
    }
}

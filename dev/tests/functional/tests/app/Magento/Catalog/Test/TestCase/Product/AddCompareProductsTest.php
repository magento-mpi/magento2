<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Page\Product\CatalogProductCompare;
use Magento\Catalog\Test\Constraint\AssertProductCompareSuccessAddMessage;

/**
 * Test creation for adding CompareProducts
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. All product types are created
 * 2. Customer created
 *
 * Steps:
 * 1. Navigate to front-end
 * 1.1 If present data for customer, login as test customer
 * 2. Open product page of test product(s) and click "Add to Compare" button
 * 3. Assert success message is present on page
 * 4. Navigate to compare page(click "compare product" link at the top of the page)
 * 5. Perform all asserts
 *
 * @group Compare_Products_(MX)
 * @ZephyrId MAGETWO-25843
 */
class AddCompareProductsTest extends AbstractCompareProductsTest
{
    /**
     * Catalog product compare page
     *
     * @var CatalogProductCompare
     */
    protected $catalogProductCompare;

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogProductCompare $catalogProductCompare
     * @param CatalogProductView $catalogProductView
     * @param CustomerAccountLogin $customerAccountLogin
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CatalogProductCompare $catalogProductCompare,
        CatalogProductView $catalogProductView,
        CustomerAccountLogin $customerAccountLogin
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductCompare = $catalogProductCompare;
        $this->catalogProductView = $catalogProductView;
        $this->customerAccountLogin = $customerAccountLogin;
    }

    /**
     * Test creation for adding compare products
     *
     * @param string $products
     * @param string $isCustomerLoggedIn
     * @param AssertProductCompareSuccessAddMessage $assertProductCompareSuccessAddMessage
     * @return array
     */
    public function test(
        $products,
        $isCustomerLoggedIn,
        AssertProductCompareSuccessAddMessage $assertProductCompareSuccessAddMessage
    ) {
        //Steps
        $this->cmsIndex->open();
        if ($isCustomerLoggedIn == 'Yes') {
            $this->loginCustomer($this->customer);
        }
        $this->products = $this->createProducts($products);
        $this->addProducts($this->products, $assertProductCompareSuccessAddMessage);
        $this->cmsIndex->getLinksBlock()->openLink("Compare Products");

        return ['products' => $this->products];
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink("Compare Products");
        for ($i = 1; $i <= count($this->products); $i++) {
            $this->catalogProductCompare->getCompareProductsBlock()->removeProduct();
        }
    }
}

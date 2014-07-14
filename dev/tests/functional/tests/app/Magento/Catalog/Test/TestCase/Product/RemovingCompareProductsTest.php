<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Page\Product\CatalogProductCompare;

/**
 * Test creation for removing CompareProducts
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. All product types are created
 * 2. Customer created
 *
 * Steps:
 * 1. Add to Compare Product $products (as flow can be used MTA-54)
 * 2. Navigate to Compare Product page
 * 3. Click (X) icon near the $product from dataset
 * 4. Perform assertions
 *
 * @group Compare_Products_(MX)
 * @ZephyrId MAGETWO-26161
 */
class RemovingCompareProductsTest extends AbstractCompareProductsTest
{
    /**
     * Catalog product compare page
     *
     * @var CatalogProductCompare
     */
    protected $catalogProductCompare;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerInjectable $customer
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory, CustomerInjectable $customer)
    {
        parent::__prepare($fixtureFactory, $customer);
        $config = $this->fixtureFactory->createByCode('configData', ['dataSet' => 'compare_products']);
        $config->persist();
    }

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
     * @param string $removeProductIndex
     * @param string $isCustomerLoggedIn
     * @return array
     */
    public function test($products, $removeProductIndex, $isCustomerLoggedIn)
    {
        //Steps
        $this->cmsIndex->open();
        if ($isCustomerLoggedIn == 'Yes') {
            $this->loginCustomer($this->customer);
        }
        $this->products = $this->createProducts($products);
        $this->addProducts($this->products);
        $this->cmsIndex->getLinksBlock()->openLink("Compare Products");
        $this->removeProduct($removeProductIndex);

        return ['product' => $this->products[$removeProductIndex], 'countProducts' => count($this->products)];
    }

    /**
     * Remove product from compare product list
     *
     * @param int $removeProduct
     * @return void
     */
    protected function removeProduct($removeProduct)
    {
        $this->catalogProductCompare->getCompareProductsBlock()->removeProduct($removeProduct + 2);
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        if (count($this->products) > 1) {
            $this->cmsIndex->open();
            $this->cmsIndex->getLinksBlock()->openLink("Compare Products");
            for ($i = 1; $i < count($this->products); $i++) {
                $this->catalogProductCompare->getCompareProductsBlock()->removeProduct();
            }
        }
    }
}

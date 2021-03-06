<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Magento\Catalog\Test\Page\Product\CatalogProductCompare;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Fixture\FixtureFactory;

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
 * 1. Add to Compare Product $products
 * 2. Navigate to Compare Product page
 * 3. Click (X) icon near the $product from dataset
 * 4. Perform assertions
 *
 * @group Compare_Products_(MX)
 * @ZephyrId MAGETWO-26161
 */
class DeleteCompareProductsTest extends AbstractCompareProductsTest
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
     * Test creation for delete product from compare products list
     *
     * @param string $products
     * @param string $removeProductIndex
     * @param string $isCustomerLoggedIn
     * @param CatalogProductCompare $catalogProductCompare
     * @return array
     */
    public function test(
        $products,
        $removeProductIndex,
        $isCustomerLoggedIn,
        CatalogProductCompare $catalogProductCompare
    ) {
        //Steps
        $this->catalogProductCompare = $catalogProductCompare;
        $this->cmsIndex->open();
        if ($isCustomerLoggedIn == 'Yes') {
            $this->loginCustomer($this->customer);
        }
        $this->products = $this->createProducts($products);
        $this->addProducts($this->products);
        $this->cmsIndex->getLinksBlock()->openLink("Compare Products");
        $this->catalogProductCompare->getCompareProductsBlock()->removeProduct($removeProductIndex);

        return ['product' => $this->products[$removeProductIndex - 1], 'countProducts' => count($this->products)];
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
            $this->catalogProductCompare->getCompareProductsBlock()->removeAllProducts();
        }
    }
}

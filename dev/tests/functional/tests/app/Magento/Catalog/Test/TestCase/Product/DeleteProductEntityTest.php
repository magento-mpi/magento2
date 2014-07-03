<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Cover DeleteProductEntity with functional tests designed for automation
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create product according to dataset
 *
 * Steps:
 * 1. Login to backend
 * 2. Navigate Products->Catalog
 * 3. Select products created in preconditions
 * 4. Select delete from mass-action
 * 5. Submit form
 * 6. Perform asserts
 *
 * @group Products_(MX)
 * @ZephyrId MAGETWO-23272
 */
class DeleteProductEntityTest extends Injectable
{
    /**
     * Product page with a grid
     *
     * @var CatalogProductIndex
     */
    protected $catalogProductIndex;

    /**
     * Injection data
     *
     * @param CatalogProductIndex $catalogProductIndexPage
     * @return void
     */
    public function __inject(CatalogProductIndex $catalogProductIndexPage)
    {
        $this->catalogProductIndex = $catalogProductIndexPage;
    }

    /**
     * Run delete product test
     *
     * @param string $products
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function test($products, FixtureFactory $fixtureFactory)
    {
        //Steps
        $products = explode(',', $products);
        $deleteProducts = [];
        foreach ($products as &$product) {
            list($fixture, $dataSet) = explode('::', $product);
            $product = $fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            $product->persist();
            $deleteProducts[] = ['sku' => $product->getSku()];
        }
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getProductGrid()->delete($deleteProducts);
        return ['product' => $products];
    }
}

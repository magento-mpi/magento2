<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Flow:
 *
 * Precondition:
 * 1. Product is created
 *
 * Steps:
 * 1. Login to backend
 * 2. Navigate to PRODUCTS > Catalog
 * 3. Click Product from grid
 * 4. Click "Save & Duplicate"
 * 5. Perform asserts
 *
 * @group Products_(CS)
 * @ZephyrId MAGETWO-23294
 */
class DuplicateProductEntityTest extends Injectable
{
    /**
     * Category fixture.
     *
     * @var CatalogCategory
     */
    protected $category;

    /**
     * Product page with a grid.
     *
     * @var CatalogProductIndex
     */
    protected $productGrid;

    /**
     * Page to update a product.
     *
     * @var CatalogProductEdit
     */
    protected $editProductPage;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Prepare data.
     *
     * @param CatalogCategory $category
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductEdit $editProductPage
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(
        CatalogCategory $category,
        CatalogProductIndex $productGrid,
        CatalogProductEdit $editProductPage,
        FixtureFactory $fixtureFactory
    ) {
        $this->category = $category;
        $this->category->persist();
        $this->productGrid = $productGrid;
        $this->editProductPage = $editProductPage;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Run test duplicate product entity.
     *
     * @param string $productType
     * @return array
     */
    public function test($productType)
    {
        // Precondition
        $product = $this->createProduct($productType);

        // Steps
        $filter = ['sku' => $product->getSku()];
        $this->productGrid->open();
        $this->productGrid->getProductGrid()->searchAndOpen($filter);
        $this->editProductPage->getFormPageActions()->saveAndDuplicate();

        return ['product' => $product];
    }

    /**
     * Creating a product according to the type of.
     *
     * @param string $productType
     * @return array
     */
    protected function createProduct($productType)
    {
        list($fixture, $dataSet) = explode('::', $productType);
        $product = $this->fixtureFactory->createByCode(
            $fixture,
            [
                'dataSet' => $dataSet,
                'data' => [
                    'category_ids' => [
                        'category' => $this->category,
                    ],
                ]
            ]
        );
        $product->persist();

        return $product;
    }
}

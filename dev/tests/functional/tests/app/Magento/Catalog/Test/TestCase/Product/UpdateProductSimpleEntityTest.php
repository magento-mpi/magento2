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
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Test Creation for UpdateProductSimpleEntity
 *
 * Test Flow:
 *
 * Precondition:
 * Category is created.
 * Product is created.
 *
 * Steps:
 * 1. Login to backend.
 * 2. Navigate to PRODUCTS -> Catalog.
 * 3. Select a product in the grid.
 * 4. Edit test value(s) according to dataset.
 * 5. Click "Save".
 * 6. Perform asserts
 *
 * @group Products_(CS)
 * @ZephyrId MAGETWO-23544
 */
class UpdateProductSimpleEntityTest extends Injectable
{
    /**
     * Simple product fixture
     *
     * @var CatalogProductSimple
     */
    protected $product;

    /**
     * Product page with a grid
     *
     * @var CatalogProductIndex
     */
    protected $productGrid;

    /**
     * Page to update a product
     *
     * @var CatalogProductEdit
     */
    protected $editProductPage;

    /**
     * Category fixture
     *
     * @var CatalogCategory
     */
    protected $category;

    /**
     * Injection data
     *
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductEdit $editProductPage
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __inject(
        CatalogProductIndex $productGrid,
        CatalogProductEdit $editProductPage,
        FixtureFactory $fixtureFactory
    ) {
        $this->product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            [
                'dataSet' => 'product_without_category',
                'data' => [
                    'category_ids' => [
                        'category' => $this->category
                    ]
                ]
            ]
        );
        $this->product->persist();

        $this->productGrid = $productGrid;
        $this->editProductPage = $editProductPage;
    }

    /**
     * Prepare data
     *
     * @param CatalogCategory $category
     * @return array
     */
    public function __prepare(CatalogCategory $category)
    {
        $category->persist();
        $this->category = $category;
        return [
            'category' => $category
        ];
    }

    /**
     * Run update product simple entity test
     *
     * @param CatalogProductSimple $product
     * @return void
     */
    public function testUpdate(CatalogProductSimple $product)
    {
        $filter = ['sku' => $this->product->getSku()];
        $this->productGrid->open()->getProductGrid()->searchAndOpen($filter);
        $productBlockForm = $this->editProductPage->getForm();
        $productBlockForm->fillProduct($product);
        $this->editProductPage->getFormAction()->save();
    }
}

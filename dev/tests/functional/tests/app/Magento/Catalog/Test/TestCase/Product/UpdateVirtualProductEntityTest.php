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
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Fixture\CatalogProductVirtual;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Test Creation for UpdateVirtualProductEntity
 *
 * Test Flow:
 *
 * Precondition:
 * 1. Category is created.
 * 2. Virtual product is created and assigned to created category.
 *
 * Steps:
 * 1. Login to backend.
 * 2. Navigate to PRODUCTS -> Catalog.
 * 3. Select a product in the grid.
 * 4. Edit test value(s) according to dataset.
 * 5. Click "Save".
 * 6. Perform asserts.
 *
 * @group Products_(CS)
 * @ZephyrId MAGETWO-26204
 */
class UpdateVirtualProductEntityTest extends Injectable
{
    /**
     * Virtual product fixture
     *
     * @var CatalogProductVirtual
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
     * Prepare data
     *
     * @param CatalogCategory $category
     * @return array
     */
    public function __prepare(CatalogCategory $category)
    {
        $category->persist();
        return [
            'category' => $category
        ];
    }

    /**
     * Injection data
     *
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductEdit $editProductPage
     * @param CatalogCategory $category
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        CatalogProductIndex $productGrid,
        CatalogProductEdit $editProductPage,
        CatalogCategory $category,
        FixtureFactory $fixtureFactory
    ) {
        $this->product = $fixtureFactory->createByCode(
            'catalogProductVirtual',
            [
                'dataSet' => 'default',
                'data' => [
                    'category_ids' => [
                        'category' => $category
                    ]
                ]
            ]
        );
        $this->product->persist();

        $this->productGrid = $productGrid;
        $this->editProductPage = $editProductPage;
    }

    /**
     * Run update product virtual entity test
     *
     * @param CatalogProductVirtual $product
     * @return void
     */
    public function test(CatalogProductVirtual $product)
    {
        // Steps
        $this->productGrid->open();
        $this->productGrid->getProductGrid()->searchAndOpen(['sku' => $this->product->getSku()]);
        $this->editProductPage->getForm()->fillProduct($product);
        $this->editProductPage->getFormAction()->save();
    }
}

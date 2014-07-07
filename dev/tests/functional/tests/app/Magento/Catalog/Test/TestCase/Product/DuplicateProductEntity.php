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
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Block\Adminhtml\Product\FormPageActions;

/**
 * Test Creation for DuplicateProductEntity
 *
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
class DuplicateProductEntity extends Injectable
{
    /**
     * Category fixture
     *
     * @var CatalogCategory
     */
    protected $category;

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
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductEdit $editProductPage
     * @return void
     */
    public function __prepare(
        CatalogCategory $category,
        CatalogProductIndex $productGrid,
        CatalogProductEdit $editProductPage
    ) {
        $this->category = $category;
        $this->category->persist();
        $this->productGrid = $productGrid;
        $this->editProductPage = $editProductPage;
    }

    /**
     * Run test duplicate product entity
     *
     * @param string $productType
     * @param FixtureFactory $fixtureFactory
     * @return InjectableFixture
     */
    public function test($productType, FixtureFactory $fixtureFactory)
    {
        // Precondition
        list($fixture, $dataSet) = explode('::', $productType);
        $product = $fixtureFactory->createByCode(
            $fixture,
            [
                'dataSet' => $dataSet,
                'data' => [
                    'category_ids' => [
                        'category' => $this->category
                    ]
                ]
            ]
        );
        $product->persist();

        // Steps
        $filter = ['sku' => $product->getSku()];
        $this->productGrid->open()
            ->getProductGrid()
            ->searchAndOpen($filter);

        $this->editProductPage
            ->getFormAction()
            ->clickSaveAction(FormPageActions::SAVE_DUPLICATE);

        return ['product' => $product];
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogCategoryEntity;
use Magento\Catalog\Test\Fixture\CatalogProductVirtual;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Test Creation for CreateVirtualProductEntity 
 *
 * Test Flow:
 * 1. Login as admin.
 * 2. Navigate to the Products > Inventory > Catalog.
 * 3. Click on "+" dropdown and select Virtual Product type.
 * 4. Fill in all data according to data set.
 * 5. Save product.
 * 6. Verify created product.
 *
 * @group Virtual_Product_(CS)
 * @ZephyrId MAGETWO-23417
 */
class CreateVirtualProductEntityTest extends Injectable
{
    /**
     * Category fixture
     *
     * @var CatalogCategoryEntity
     */
    protected $category;

    /**
     * Product page with a grid
     *
     * @var CatalogProductIndex
     */
    protected $productGrid;

    /**
     * Page to create a product
     *
     * @var CatalogProductNew
     */
    protected $newProductPage;

    /**
     * Prepare data
     *
     * @param CatalogCategoryEntity $category
     * @return array
     */
    public function __prepare(CatalogCategoryEntity $category)
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
     * @param CatalogProductNew $newProductPage
     * @return void
     */
    public function __inject(CatalogProductIndex $productGrid, CatalogProductNew $newProductPage)
    {
        $this->productGrid = $productGrid;
        $this->newProductPage = $newProductPage;
    }

    /**
     * Run create product virtual entity test
     *
     * @param CatalogProductVirtual $product
     * @param CatalogCategoryEntity $category
     * @return void
     */
    public function testCreate(CatalogProductVirtual $product, CatalogCategoryEntity $category)
    {
        // Steps
        $this->productGrid->open();
        $this->productGrid->getProductBlock()->addProduct('virtual');
        $productBlockForm = $this->newProductPage->getForm();
        $productBlockForm->fillProduct($product, $category);
        $this->newProductPage->getFormAction()->save();
    }
}

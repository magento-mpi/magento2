<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Test Creation for CreateSimpleProductEntity
 *
 * Test Flow:
 * 1. Login to the backend.
 * 2. Navigate to Products > Catalog.
 * 3. Start to create simple product.
 * 4. Fill in data according to data set.
 * 5. Save Product.
 * 6. Perform appropriate assertions.
 *
 * @group Products_(CS)
 * @ZephyrId MAGETWO-23414
 */
class CreateSimpleProductEntityTest extends Injectable
{
    /**
     * Fixture category
     *
     * @var Category
     */
    protected $category;

    /**
     * Product page with a grid
     *
     * @var CatalogProductIndex
     */
    protected $productGrid;

    /**
     * @var CatalogProductNew
     */
    protected $newProductPage;

    /**
     * Prepare data
     *
     * @param Category $category
     * @return array
     */
    public function __prepare(Category $category)
    {
        $category->persist();

        return [
            'category' => $category
        ];
    }

    /**
     * Injection data
     *
     * @param Category $category
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductNew $newProductPage
     * @return void
     */
    public function __inject(
        Category $category,
        CatalogProductIndex $productGrid,
        CatalogProductNew $newProductPage
    ) {
        $this->category = $category;
        $this->productGrid = $productGrid;
        $this->newProductPage = $newProductPage;
    }

    /**
     * Run test
     *
     * @param CatalogProductSimple $product
     * @param Category $category
     * @return void
     */
    public function testCreate(CatalogProductSimple $product, Category $category)
    {
        // Steps
        $this->productGrid->open();
        $this->productGrid->getProductBlock()->addProduct('simple');
        $productBlockForm = $this->newProductPage->getProductForm();
        $productBlockForm->setCategory($category);
        $productBlockForm->fill($product);
        $this->newProductPage->getProductPageAction()->save();
    }
}

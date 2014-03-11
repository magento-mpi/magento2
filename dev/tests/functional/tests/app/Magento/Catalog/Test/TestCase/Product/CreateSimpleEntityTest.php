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
use Magento\Catalog\Test\Page\Product\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Test Coverage for CreateProductEntity
 *
 * General Flow:
 * 1. Log in to Backend.
 * 2. Navigate to Products > Catalog.
 * 3. Start to create new product.
 * 4. Fill in data according to data set.
 * 5. Save product.
 * 6. Verify created product.
 *
 * @ticketId MAGETWO-20024
 */
class CreateSimpleEntityTest extends Injectable
{
    /**
     * @var Category
     */
    protected $category;

    /**
     * @var CatalogProductIndex
     */
    protected $productPageGrid;

    /**
     * @var CatalogProductNew
     */
    protected $newProductPage;

    /**
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
     * @param Category $category
     * @param CatalogProductIndex $productPageGrid
     * @param CatalogProductNew $newProductPage
     */
    public function __inject(
        Category $category,
        CatalogProductIndex $productPageGrid,
        CatalogProductNew $newProductPage
    ) {
        $this->category = $category;
        $this->productPageGrid = $productPageGrid;
        $this->newProductPage = $newProductPage;
    }

    /**
     * @param CatalogProductSimple $product
     * @param Category $category
     */
    public function testCreate(CatalogProductSimple $product, Category $category)
    {
        // Steps
        $this->productPageGrid->open();
        $this->productPageGrid->getProductBlock()->addProduct('simple');
        $productBlockForm = $this->newProductPage->getProductBlockForm();
        $productBlockForm->setCategory($category);
        $productBlockForm->fill($product);
        $productBlockForm->save($product);
    }
}

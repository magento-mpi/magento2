<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\TestCase\Product;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\Category;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;
use Magento\Catalog\Test\Page\Product\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Test Coverage for CreateConfigurableProductEntity
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
class CreateConfigurableEntityTest extends Injectable
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
     * @param CatalogProductConfigurable $configurable
     * @param Category $category
     */
    public function testCreate(CatalogProductConfigurable $configurable, Category $category)
    {
        // Steps
        $this->productPageGrid->open();
        $this->productPageGrid->getProductBlock()->addProduct('configurable');

        $productBlockForm = $this->newProductPage->getConfigurableBlockForm();

        $productBlockForm->setCategory($category);
        $productBlockForm->fill($configurable);
        $productBlockForm->save($configurable);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\TestCase\Product;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogCategoryEntity;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

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
     * Category fixture
     *
     * @var CatalogCategoryEntity
     */
    protected $category;

    /**
     * Backend catalog page (product grid)
     *
     * @var CatalogProductIndex
     */
    protected $productPageGrid;

    /**
     * Product page (product form)
     *
     * @var CatalogProductNew
     */
    protected $newProductPage;

    /**
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
     * @param CatalogCategoryEntity $category
     * @param CatalogProductIndex $productPageGrid
     * @param CatalogProductNew $newProductPage
     */
    public function __inject(
        CatalogCategoryEntity $category,
        CatalogProductIndex $productPageGrid,
        CatalogProductNew $newProductPage
    ) {
        $this->category = $category;
        $this->productPageGrid = $productPageGrid;
        $this->newProductPage = $newProductPage;
    }

    /**
     * Run create configurable product test
     *
     * @param CatalogProductConfigurable $configurable
     * @param CatalogCategoryEntity $category
     * @return void
     */
    public function testCreate(CatalogProductConfigurable $configurable, CatalogCategoryEntity $category)
    {
        // Steps
        $this->productPageGrid->open();
        $this->productPageGrid->getProductBlock()->addProduct('configurable');
        // Fill form
        $productBlockForm = $this->newProductPage->getConfigurableProductForm();
        $productBlockForm->setCategory($category);
        $productBlockForm->fill($configurable);
        $this->newProductPage->getFormAction()->saveProduct($this->newProductPage, $configurable);
    }
}

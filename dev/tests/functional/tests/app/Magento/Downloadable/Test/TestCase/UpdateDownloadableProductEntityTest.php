<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Downloadable\Test\Fixture\CatalogProductDownloadable;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;

/**
 * Test Creation for Update DownloadableProductEntity
 *
 * Test Flow:
 * 1. Login to backend.
 * 2. Navigate to PRODUCTS > Catalog.
 * 3. Search and open product in the grid.
 * 4. Edit test value(s) according to dataset.
 * 5. Click "Save".
 * 6. Perform asserts.
 *
 * @group Downloadable_Product_(MX)
 * @ZephyrId MAGETWO-24775
 */
class UpdateDownloadableProductEntityTest extends Injectable
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
    protected $catalogProductIndex;

    /**
     * New product page on backend
     *
     * @var CatalogProductNew
     */
    protected $catalogProductNew;

    /**
     * Persist category
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
     * Filling objects of the class
     *
     * @param Category $category
     * @param CatalogProductIndex $catalogProductIndexNewPage
     * @param CatalogProductNew $catalogProductNewPage
     */
    public function __inject(
        Category $category,
        CatalogProductIndex $catalogProductIndexNewPage,
        CatalogProductNew $catalogProductNewPage
    ) {
        $this->category = $category;
        $this->catalogProductIndex = $catalogProductIndexNewPage;
        $this->catalogProductNew = $catalogProductNewPage;
    }

    /**
     * Test create downloadable product
     *
     * @param CatalogProductDownloadable $product
     * @param Category $category
     */
    public function testUpdateDownloadableProduct(CatalogProductDownloadable $product, Category $category)
    {
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getProductBlock()->addProduct('downloadable');
        $productBlockForm = $this->catalogProductNew->getForm();
        $productBlockForm->fillProduct($product, $category);
        $this->catalogProductNew->getFormAction()->save();
    }
}

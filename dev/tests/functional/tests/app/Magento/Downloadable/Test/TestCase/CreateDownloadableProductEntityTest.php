<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Downloadable\Test\Fixture\CatalogProductDownloadable;

/**
 * Test Creation for Create DownloadableProductEntity
 *
 * Test Flow:
 * 1. Log in to Backend.
 * 2. Navigate to Products > Catalog.
 * 3. Start to create new Downloadable product.
 * 4. Fill in data according to data set.
 * 5. Fill Downloadable Information tab according to data set.
 * 6. Save product.
 * 7. Verify created product.
 *
 * @group Downloadable_Product_(CS)
 * @ZephyrId MAGETWO-23425
 */
class CreateDownloadableProductEntityTest extends Injectable
{
    /**
     * Fixture category
     *
     * @var CatalogCategory
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
     * Filling objects of the class
     *
     * @param CatalogCategory $category
     * @param CatalogProductIndex $catalogProductIndexNewPage
     * @param CatalogProductNew $catalogProductNewPage
     * @return void
     */
    public function __inject(
        CatalogCategory $category,
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
     * @param CatalogCategory $category
     * @return void
     */
    public function testCreateDownloadableProduct(CatalogProductDownloadable $product, CatalogCategory $category)
    {
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getGridPageActionBlock()->addProduct('downloadable');
        $productBlockForm = $this->catalogProductNew->getForm();
        $productBlockForm->fillProduct($product, $category);
        $this->catalogProductNew->getFormAction()->save();
    }
}

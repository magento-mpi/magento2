<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogCategoryEntity;
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
     * @var CatalogCategoryEntity
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
     * Filling objects of the class
     *
     * @param CatalogCategoryEntity $category
     * @param CatalogProductIndex $catalogProductIndexNewPage
     * @param CatalogProductNew $catalogProductNewPage
     */
    public function __inject(
        CatalogCategoryEntity $category,
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
     * @param CatalogCategoryEntity $category
     */
    public function testCreateDownloadableProduct(CatalogProductDownloadable $product, CatalogCategoryEntity $category)
    {
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getProductBlock()->addProduct('downloadable');
        $productBlockForm = $this->catalogProductNew->getForm();
        $productBlockForm->fillProduct($product, $category);
        $this->catalogProductNew->getFormAction()->save();
    }
}

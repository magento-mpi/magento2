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
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex as CatalogProductIndexPage;
use Magento\Catalog\Test\Page\Product\CatalogProductNew;


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
 * @ZephyrId MTA-15
 */
class CreateDownloadableProductEntityTest extends Injectable
{
    /**
     * @var Category
     */
    protected $category;
    /**
     * @var CatalogProductIndexPage
     */
    protected $catalogProductIndex;
    /**
     * @var CatalogProductNew
     */
    protected $catalogProductNew;

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
     * @param CatalogProductIndexPage $catalogProductIndex
     * @param CatalogProductNew $catalogProductNew
     */
    public function __inject(
        Category $category,
        CatalogProductIndexPage $catalogProductIndexNewPage,
        CatalogProductNew $catalogProductNewPage
    ) {
        $this->category = $category;
        $this->catalogProductIndex = $catalogProductIndexNewPage;
        $this->catalogProductNew = $catalogProductNewPage;
    }

    /**
     * @param CatalogProductDownloadable $product
     * @param Category $category
     */
    public function testCreateDownloadableProduct(CatalogProductDownloadable $product, Category $category)
    {
        // Steps
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getGridPageActions()->setTypeProduct("downloadable");
        $this->catalogProductIndex->getGridPageActions()->addNew();
        $productBlockForm = $this->catalogProductNew->getProductBlockForm();
        //$product->getDownloadableLinks();
        $productBlockForm->setCategory($category);
        $productBlockForm->fill($product);
        $productBlockForm->save($product);
    }
}

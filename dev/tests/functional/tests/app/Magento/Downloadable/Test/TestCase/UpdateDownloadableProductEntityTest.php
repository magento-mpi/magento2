<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Downloadable\Test\Fixture\CatalogProductDownloadable;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Test Creation for Update DownloadableProductEntity
 *
 * Test Flow:
 *
 * Precondition:
 * Category is created.
 * Product is created(before each variation).
 *
 * Steps:
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
     * Downloadable product fixture
     *
     * @var CatalogProductDownloadable
     */
    protected $product;

    /**
     * Product page with a grid
     *
     * @var CatalogProductIndex
     */
    protected $catalogProductIndex;

    /**
     * Edit product page on backend
     *
     * @var CatalogProductEdit
     */
    protected $catalogProductEdit;

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
     * @param CatalogProductIndex $catalogProductIndexNewPage
     * @param CatalogProductEdit $catalogProductEditPage
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        CatalogProductIndex $catalogProductIndexNewPage,
        CatalogProductEdit $catalogProductEditPage,
        FixtureFactory $fixtureFactory
    ) {
        $this->product = $fixtureFactory->createByCode(
            'catalogProductDownloadable',
            ['dataSet' => 'customDefault']
        );
        $this->product->persist();
        $this->catalogProductIndex = $catalogProductIndexNewPage;
        $this->catalogProductEdit = $catalogProductEditPage;
    }

    /**
     * Test update downloadable product
     *
     * @param CatalogProductDownloadable $product
     * @param CatalogCategory $category
     * @return void
     */
    public function testUpdateDownloadableProduct(CatalogProductDownloadable $product, CatalogCategory $category)
    {
        $filter = ['sku' => $this->product->getSku()];
        $this->catalogProductIndex->open()->getProductGrid()->searchAndOpen($filter);
        $productBlockForm = $this->catalogProductEdit->getForm();
        $productBlockForm->fillProduct($product, $category);
        $this->catalogProductEdit->getFormAction()->save();
    }
}

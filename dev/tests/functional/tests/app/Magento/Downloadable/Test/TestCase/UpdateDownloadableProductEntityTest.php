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
use Magento\Catalog\Test\Fixture\CatalogCategoryEntity;
use Magento\Downloadable\Test\Fixture\CatalogProductDownloadable;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

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
     * @var CatalogCategoryEntity
     */
    protected $category;

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
     * New product page on backend
     *
     * @var CatalogProductEdit
     */
    protected $catalogProductEdit;

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
     * @param CatalogProductEdit $catalogProductEditPage
     * @param FixtureFactory $fixtureFactory
     */
    public function __inject(
        CatalogCategoryEntity $category,
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
        $this->category = $category;
    }

    /**
     * Test create downloadable product
     *
     * @param CatalogProductDownloadable $product
     * @param CatalogCategoryEntity $category
     */
    public function testUpdateDownloadableProduct(CatalogProductDownloadable $product, CatalogCategoryEntity $category)
    {
        $filter = ['sku' => $this->product->getSku()];
        $this->catalogProductIndex->open()->getProductGrid()->searchAndOpen($filter);
        $productBlockForm = $this->catalogProductEdit->getForm();
        $productBlockForm->fillProduct($product, $category);
        $this->catalogProductEdit->getFormAction()->save();
    }
}

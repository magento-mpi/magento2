<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Test Creation for Update BundleProductEntity
 *
 * Test Flow:
 *
 * Precondition:
 * 1. Category is created.
 * 2. Bundle product is created.
 *
 * Steps
 * 1. Login to backend.
 * 2. Navigate to PRODUCTS > Catalog.
 * 3. Select a product in the grid.
 * 4. Edit test value(s) according to dataset.
 * 5. Click "Save".
 * 6. Perform asserts
 *
 *
 * @group Bundle_Product_(MX)
 * @ZephyrId MAGETWO-26195
 */
class UpdateBundleProductEntityTest extends Injectable
{
    /**
     * Page product on backend
     *
     * @var CatalogProductIndex
     */
    protected $catalogProductIndex;

    /**
     * Edit page on backend
     *
     * @var CatalogProductEdit
     */
    protected $catalogProductEdit;

    /**
     * Filling objects of the class
     *
     * @param CatalogProductIndex $catalogProductIndexNewPage
     * @param CatalogProductEdit $catalogProductEditPage
     * @return void
     */
    public function __inject(CatalogProductIndex $catalogProductIndexNewPage,CatalogProductEdit $catalogProductEditPage)
    {
        $this->catalogProductIndex = $catalogProductIndexNewPage;
        $this->catalogProductEdit = $catalogProductEditPage;
    }

    /**
     * Test update bundle product
     *
     * @param CatalogProductBundle $product
     * @param CatalogProductBundle $originalProduct
     * @return array
     */
    public function test(CatalogProductBundle $product, CatalogProductBundle $originalProduct)
    {
        $originalProduct->persist();
        $this->catalogProductIndex->open();
        $filter = ['sku' => $originalProduct->getSku()];
        $this->catalogProductIndex->getProductGrid()->searchAndOpen($filter);
        $this->catalogProductEdit->getForm()->fillProduct($product);
        $this->catalogProductEdit->getFormAction()->save();

        return ['originalProduct' => $originalProduct];
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\GroupedProduct\Test\Fixture\CatalogProductGrouped;

/**
 * Test Creation for Update GroupedProductEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create Grouped Product.
 *
 * Steps:
 * 1. Login to the backend.
 * 2. Navigate to Products > Catalog.
 * 3. Open grouped product from preconditions.
 * 4. Fill in data according to dataset.
 * 5. Save the Product.
 * 6. Perform all assertions.
 *
 * @group Grouped_Product_(MX)
 * @ZephyrId MAGETWO-26462
 */
class UpdateGroupedProductEntityTest extends Injectable
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
     * Test update grouped product
     *
     * @param CatalogProductGrouped $product
     * @param CatalogProductGrouped $originalProduct
     * @return void
     */
    public function test(CatalogProductGrouped $product, CatalogProductGrouped $originalProduct)
    {
        $originalProduct->persist();
        $this->catalogProductIndex->open();
        $filter = ['sku' => $originalProduct->getSku()];
        $this->catalogProductIndex->getProductGrid()->searchAndOpen($filter);
        $this->catalogProductEdit->getForm()->fill($product);
        $this->catalogProductEdit->getFormAction()->save();
    }
}

<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GroupedProduct\Test\TestCase;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\GroupedProduct\Test\Fixture\GroupedProductInjectable;
use Mtf\TestCase\Injectable;

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
    public function __inject(
        CatalogProductIndex $catalogProductIndexNewPage,
        CatalogProductEdit $catalogProductEditPage
    ) {
        $this->catalogProductIndex = $catalogProductIndexNewPage;
        $this->catalogProductEdit = $catalogProductEditPage;
    }

    /**
     * Test update grouped product
     *
     * @param GroupedProductInjectable $product
     * @param GroupedProductInjectable $originalProduct
     * @return void
     */
    public function test(GroupedProductInjectable $product, GroupedProductInjectable $originalProduct)
    {
        // Precondition
        $originalProduct->persist();

        // Steps
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getProductGrid()->searchAndOpen(['sku' => $originalProduct->getSku()]);
        $this->catalogProductEdit->getProductForm()->fill($product);
        $this->catalogProductEdit->getFormPageActions()->save();
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;

/**
 * Test Creation for CreateBundleProductEntity 
 *
 * Test Flow:
 * 1. Login as admin
 * 2. Navigate to the Products>Inventory>Catalog
 * 3. Click on "+" dropdown and select Bundle Product type
 * 4. Fill in all data according to data set
 * 5. Save product
 * 6. Verify created product
 *
 * @group Bundle_Product_(CS)
 * @ZephyrId MAGETWO-24118
 */
class CreateBundleProductEntityTest extends Injectable
{
    /**
     * Page product on backend
     *
     * @var CatalogProductIndex
     */
    protected $catalogProductIndex;

    /**
     * New page on backend
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
     * @param CatalogProductIndex $catalogProductIndexNewPage
     * @param CatalogProductNew $catalogProductNewPage
     * @return void
     */
    public function __inject(
        CatalogProductIndex $catalogProductIndexNewPage,
        CatalogProductNew $catalogProductNewPage
    ) {
        $this->catalogProductIndex = $catalogProductIndexNewPage;
        $this->catalogProductNew = $catalogProductNewPage;
    }

    /**
     * Test create bundle product
     *
     * @param CatalogProductBundle $product
     * @param CatalogCategory $category
     * @return void
     */
    public function test(CatalogProductBundle $product, CatalogCategory $category)
    {
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getGridPageActionBlock()->addProduct('bundle');
        $productBlockForm = $this->catalogProductNew->getForm();
        $productBlockForm->fill($product, null, $category);
        $this->catalogProductNew->getFormAction()->save();
    }
}

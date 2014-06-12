<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\GroupedProduct\Test\Fixture\CatalogProductGrouped;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;

/**
 * Test Creation for CreateGroupedProductEntity
 *
 * Preconditions:
 * Products from "product/dataSet" column is created
 * For simple product: https://wiki.corp.x.com/download/attachments/82015445/CatalogProductSimple.php
 * For virtual product: https://wiki.corp.x.com/download/attachments/82015445/CatalogProductVirtual.php

 * Test Flow:
 * 1. Login to the backend.
 * 2. Navigate to Products > Catalog.
 * 3. Start to create Grouped Product.
 * 4. Fill in data according to data set.
 * 5. Click "Add Products to Group" button and add product from "product/dataSet" column.
 * 6. Click "Add Selected Product" button
 * 7. Save the Product.
 * 8. Perform assertions.
 *
 * @group Grouped_Product_(MX)
 * @ZephyrId MAGETWO-24877
 */
class CreateGroupedProductEntityTest extends Injectable
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
     * Test create grouped product
     *
     * @param CatalogProductGrouped $product
     * @param CatalogCategory $category
     * @return void
     */
    public function testCreateGroupedProduct(CatalogProductGrouped $product, CatalogCategory $category)
    {
        //Steps
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getProductBlock()->addProduct('grouped');
        $productBlockForm = $this->catalogProductNew->getForm();
        $productBlockForm->fillProduct($product, $category);
        $this->catalogProductNew->getFormAction()->save();
    }
}

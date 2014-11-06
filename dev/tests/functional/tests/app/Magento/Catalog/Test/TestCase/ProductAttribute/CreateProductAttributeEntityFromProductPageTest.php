<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\ProductAttribute;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Flow:
 *
 * @group Product_Attributes_(MX)
 * @ZephyrId
 */
class CreateProductAttributeEntityFromProductPageTest extends Injectable
{
    /**
     * Product page with a grid.
     *
     * @var CatalogProductIndex
     */
    protected $catalogProductIndex;

    /**
     * Page to update a product.
     *
     * @var CatalogProductEdit
     */
    protected $catalogProductEdit;

    /**
     * CatalogProductAttributeIndex page.
     *
     * @var CatalogProductAttributeIndex
     */
    protected $catalogProductAttributeIndex;

    /**
     * CatalogProductAttributeNew page.
     *
     * @var CatalogProductAttributeNew
     */
    protected $catalogProductAttributeNew;

    /**
     * CatalogProductAttribute fixture.
     *
     * @var CatalogProductAttribute
     */
    protected $attribute;

    /**
     * Prepare product for test.
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'product_with_category_with_anchor']
        );
        $product->persist();
        return ['product' => $product];
    }

    /**
     * Inject data.
     *
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductEdit $catalogProductEdit
     * @param CatalogProductAttributeIndex $catalogProductAttributeIndex
     * @param CatalogProductAttributeNew $catalogProductAttributeNew
     * @return void
     */
    public function __inject(
        CatalogProductIndex $catalogProductIndex,
        CatalogProductEdit $catalogProductEdit,
        CatalogProductAttributeIndex $catalogProductAttributeIndex,
        CatalogProductAttributeNew $catalogProductAttributeNew
    ) {
        $this->catalogProductIndex = $catalogProductIndex;
        $this->catalogProductEdit = $catalogProductEdit;
        $this->catalogProductAttributeIndex = $catalogProductAttributeIndex;
        $this->catalogProductAttributeNew = $catalogProductAttributeNew;
    }

    /**
     * Run CreateProductAttributeEntity from product page test.
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductAttribute $attribute
     * @return void
     */
    public function test(CatalogProductSimple $product, CatalogProductAttribute $attribute)
    {
        // Steps:
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getProductGrid()->searchAndOpen(['sku' => $product->getSku()]);
        $productForm = $this->catalogProductEdit->getProductForm();
        $productForm->addNewAttribute();
        $productForm->fillAttributeForm($attribute);
        $productForm->getCustomAttributeBlock($attribute)->setValue();
        $this->catalogProductEdit->getFormPageActions()->save($product);

        // Prepare data for tearDown:
        $this->attribute = $attribute;
    }

    /**
     * Delete attribute after test.
     *
     * @return void
     */
    public function tearDown()
    {
        $filter = ['attribute_code' => $this->attribute->getAttributeCode()];
        if ($this->catalogProductAttributeIndex->open()->getGrid()->isRowVisible($filter)) {
            $this->catalogProductAttributeIndex->open()->getGrid()->searchAndOpen($filter);
            $this->catalogProductAttributeNew->getPageActions()->delete();
        }
    }
}

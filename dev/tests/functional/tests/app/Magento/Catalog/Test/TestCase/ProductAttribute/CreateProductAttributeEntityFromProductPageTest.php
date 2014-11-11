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
 * Preconditions:
 * 1. Create Product.
 *
 * Steps:
 * 1. Log in to Backend.
 * 2. Navigate to Products>Catalog.
 * 3. Open product created in preconditions.
 * 4. Click add new attribute.
 * 5. Fill out fields data according to data set.
 * 6. Save Product Attribute.
 * 7. Perform appropriate assertions.
 *
 * @group Product_Attributes_(MX)
 * @ZephyrId MAGETWO-30528
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
     * Product attribute index page.
     *
     * @var CatalogProductAttributeIndex
     */
    protected $catalogProductAttributeIndex;

    /**
     * New product attribute page.
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
     * FixtureFactory object.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Prepare data for test.
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
        $this->fixtureFactory = $fixtureFactory;
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
     * @return CatalogProductSimple $product
     */
    public function test(CatalogProductSimple $product, CatalogProductAttribute $attribute)
    {
        // Steps:
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getProductGrid()->searchAndOpen(['sku' => $product->getSku()]);
        $productForm = $this->catalogProductEdit->getProductForm();
        $productForm->addNewAttribute();
        $productForm->fillAttributeForm($attribute);

        // Prepare for assertions:
        $this->setDefaultAttributeValue($attribute);
        $this->catalogProductEdit->getFormPageActions()->save();

        // Prepare data for tearDown:
        $this->attribute = $attribute;
        return ['product' => $product];
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
            $this->catalogProductAttributeIndex->getGrid()->searchAndOpen($filter);
            $this->catalogProductAttributeNew->getPageActions()->delete();
        }
    }

    /**
     * Set Custom Attribute Value.
     *
     * @param CatalogProductAttribute $attribute
     * @return void
     */
    protected function setDefaultAttributeValue(CatalogProductAttribute $attribute)
    {
        $product = $this->fixtureFactory->createByCode(
            'catalogProductSimple',
            ['data' => ['custom_attribute' => $attribute]]
        );
        $this->catalogProductEdit->getProductForm()->fill($product);
    }
}

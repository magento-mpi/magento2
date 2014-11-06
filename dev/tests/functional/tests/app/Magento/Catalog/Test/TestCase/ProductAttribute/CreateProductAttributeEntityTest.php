<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\ProductAttribute;

use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Catalog\Test\Constraint\AssertProductAttributeSaveMessage;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeNew;

/**
 * Test Creation for CreateProductAttributeEntity
 *
 * Test Flow:
 * 1. Log in to Backend.
 * 2. Navigate to Stores > Attributes > Product.
 * 3. Start to create new Product Attribute.
 * 4. Fill out fields data according to data set.
 * 5. Save Product Attribute.
 * 6. Perform appropriate assertions.
 *
 * @group Product_Attributes_(CS)
 * @ZephyrId MAGETWO-24767
 */
class CreateProductAttributeEntityTest extends Injectable
{
    /**
     * CatalogProductAttribute object.
     *
     * @var CatalogProductAttribute
     */
    protected $attribute;

    /**
     * AdminCache page.
     *
     * @var AdminCache
     */
    protected $adminCache;

    /**
     * CatalogProductAttributeIndex page.
     *
     * @var CatalogProductAttributeIndex
     */
    protected $attributeIndex;

    /**
     * CatalogProductAttributeNew page.
     *
     * @var CatalogProductAttributeNew
     */
    protected $attributeNew;

    /**
     * CatalogProductSetIndex page.
     *
     * @var CatalogProductSetIndex
     */
    protected $catalogProductSetIndex;

    /**
     * CatalogProductSetEdit page.
     *
     * @var CatalogProductSetEdit
     */
    protected $catalogProductSetEdit;

    /**
     * FixtureFactory object.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Injection data.
     *
     * @param CatalogProductAttributeIndex $attributeIndex
     * @param CatalogProductAttributeNew $attributeNew
     * @param AdminCache $adminCache
     * @param CatalogProductSetIndex $catalogProductSetIndex
     * @param CatalogProductSetEdit $catalogProductSetEdit
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        CatalogProductAttributeIndex $attributeIndex,
        CatalogProductAttributeNew $attributeNew,
        AdminCache $adminCache,
        CatalogProductSetIndex $catalogProductSetIndex,
        CatalogProductSetEdit $catalogProductSetEdit,
        FixtureFactory $fixtureFactory
    ) {
        $this->attributeIndex = $attributeIndex;
        $this->attributeNew = $attributeNew;
        $this->adminCache = $adminCache;
        $this->catalogProductSetIndex = $catalogProductSetIndex;
        $this->catalogProductSetEdit = $catalogProductSetEdit;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Run CreateProductAttributeEntity test
     *
     * @param CatalogProductAttribute $productAttribute
     * @param CatalogProductAttributeIndex $attributeIndex
     * @param CatalogProductAttributeNew $attributeNew
     * @param CatalogAttributeSet $productTemplate
     * @param AssertProductAttributeSaveMessage $assertProductAttributeSaveMessage
     * @return array
     */
    public function testCreateProductAttribute(
        CatalogProductAttribute $productAttribute,
        CatalogProductAttributeIndex $attributeIndex,
        CatalogProductAttributeNew $attributeNew,
        CatalogAttributeSet $productTemplate,
        AssertProductAttributeSaveMessage $assertProductAttributeSaveMessage
    ) {
        //Precondition
        $productTemplate->persist();

        //Steps
        $attributeIndex->open();
        $attributeIndex->getPageActionsBlock()->addNew();
        $attributeNew->getAttributeForm()->fill($productAttribute);
        $attributeNew->getPageActions()->save();
        $assertProductAttributeSaveMessage->processAssert($this->attributeIndex);

        // Move attribute to default attribute set and create product for asserts:
        $this->attribute = $productAttribute;
        $this->moveAttributeToAttributeSet($productAttribute, $productTemplate);
        $product = $this->createProductForAsserts($productTemplate);

        return ['attribute' => $productAttribute, 'product' => $product];
    }

    /**
     * Move attribute to attribute set.
     *
     * @param CatalogProductAttribute $attribute
     * @param CatalogAttributeSet $productTemplate
     * @return void
     */
    protected function moveAttributeToAttributeSet(
        CatalogProductAttribute $attribute,
        CatalogAttributeSet $productTemplate
    ) {
        $filterAttribute = ['set_name' => $productTemplate->getAttributeSetName()];
        $this->catalogProductSetIndex->open()->getGrid()->searchAndOpen($filterAttribute);

        $this->catalogProductSetEdit->getAttributeSetEditBlock()->moveAttribute(
            $attribute->getData(),
            'Product Details'
        );
    }

    /**
     * Create product for asserts.
     *
     * @param CatalogAttributeSet $productTemplate
     * @return CatalogProductSimple
     */
    protected function createProductForAsserts(CatalogAttributeSet $productTemplate)
    {
        $this->catalogProductSetEdit->getPageActions()->save();

        $product = $this->fixtureFactory->createByCode(
            'catalogProductSimple',
            [
                'dataSet' => 'product_with_category_with_anchor',
                'data' => [
                    'attribute_set_id' => ['attribute_set' => $productTemplate],
                ],
            ]
        );
        $product->persist();

        return $product;
    }

    /**
     * Delete attribute after test.
     *
     * @return void
     */
    public function tearDown()
    {
        $filter = ['attribute_code' => $this->attribute->getAttributeCode()];
        if ($this->attributeIndex->open()->getGrid()->isRowVisible($filter)) {
            $this->attributeIndex->open()->getGrid()->searchAndOpen($filter);
            $this->attributeNew->getPageActions()->delete();
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\ProductAttribute;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeNew;
use Mtf\TestCase\Scenario;

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
class CreateProductAttributeEntityTest extends Scenario
{
    /**
     * CatalogProductAttribute object.
     *
     * @var CatalogProductAttribute
     */
    protected $attribute;

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
     * Injection data.
     *
     * @param CatalogProductAttributeIndex $attributeIndex
     * @param CatalogProductAttributeNew $attributeNew
     * @return void
     */
    public function __inject(
        CatalogProductAttributeIndex $attributeIndex,
        CatalogProductAttributeNew $attributeNew
    ) {
        $this->attributeIndex = $attributeIndex;
        $this->attributeNew = $attributeNew;
    }

    /**
     * Run CreateProductAttributeEntity test.
     *
     * @param CatalogProductAttribute $productAttribute
     * @return array
     */
    public function testCreateProductAttribute(CatalogProductAttribute $productAttribute)
    {
        $this->attribute = $productAttribute;
        $this->executeScenario();
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
            $this->attributeIndex->getGrid()->searchAndOpen($filter);
            $this->attributeNew->getPageActions()->delete();
        }
    }
}

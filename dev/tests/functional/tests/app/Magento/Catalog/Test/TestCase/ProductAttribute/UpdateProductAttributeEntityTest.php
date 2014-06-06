<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\ProductAttribute;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeNew;

/**
 * Test Creation for UpdateProductAttributeEntity
 *
 * Preconditions:
 * Preset : AttributeOptions
 * 1. Attribute is created (Attribute)
 * 2. Attribute set is created (Product Template)
 * 3. Product is created (Simple, Configurable)
 *
 * Test Flow:
 * 1. Log in to Backend.
 * 2. Navigate to Stores > Attributes > Product.
 * 3. Select created attribute from preconditions
 * 4. Fill data from dataset
 * 5. Click 'Save Attribute' button
 * 6. Perform all assertions
 *
 * @group Product_Attributes_(MX)
 * @ZephyrId MAGETWO-23459
 */
class UpdateProductAttributeEntityTest extends Injectable
{
    /**
     * Run UpdateProductAttributeEntity test
     *
     * @param CatalogProductAttribute $attribute
     * @param CatalogAttributeSet $productTemplate
     * @param CatalogProductAttributeIndex $attributeIndex
     * @param CatalogProductAttributeNew $attributeNew
     * @param CatalogProductAttribute $productAttribute
     * @param string $product
     * @return void
     */
    public function testUpdateProductAttribute(
        CatalogProductAttribute $attribute,
        CatalogAttributeSet $productTemplate,
        CatalogProductAttributeIndex $attributeIndex,
        CatalogProductAttributeNew $attributeNew,
        CatalogProductAttribute $productAttribute,
        $product
    ) {
        //Precondition
        $productTemplate->persist();
        $attribute->persist();

        $filter = [
            'frontend_label' => $attribute->getFrontendLabel(),
        ];

        //Steps
        $attributeIndex->open();
        $attributeIndex->getBlockAttributeGrid()->searchAndOpen($filter);
        $attributeNew->getAttributeForm()->fill($productAttribute);
        $attributeNew->getPageActions()->save();
    }
}

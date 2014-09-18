<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\ProductAttribute;

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
     * Run CreateProductAttributeEntity test
     *
     * @param CatalogProductAttribute $productAttribute
     * @param CatalogProductAttributeIndex $attributeIndex
     * @param CatalogProductAttributeNew $attributeNew
     * @param CatalogAttributeSet $productTemplate
     * @return array
     */
    public function testCreateProductAttribute(
        CatalogProductAttribute $productAttribute,
        CatalogProductAttributeIndex $attributeIndex,
        CatalogProductAttributeNew $attributeNew,
        CatalogAttributeSet $productTemplate
    ) {
        //Precondition
        $productTemplate->persist();

        //Steps
        $attributeIndex->open();
        $attributeIndex->getPageActionsBlock()->addNew();
        $attributeNew->getAttributeForm()->fill($productAttribute);
        $attributeNew->getPageActions()->save();
        return ['attribute' => $productAttribute];
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\ProductAttribute;

use Magento\Catalog\Test\Fixture\CatalogProductTemplate;
use Mtf\TestCase\Injectable;
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
     * @param CatalogProductAttribute $attribute
     * @param CatalogProductAttributeIndex $attributeIndex
     * @param CatalogProductAttributeNew $attributeNew
     * @param CatalogProductTemplate $productTemplate
     * @param string $product
     * @return void
     */
    public function testCreateProductAttribute(
        CatalogProductAttribute $attribute,
        CatalogProductAttributeIndex $attributeIndex,
        CatalogProductAttributeNew $attributeNew,
        CatalogProductTemplate $productTemplate,
        $product
    ) {
        //Precondition
        $productTemplate->persist();

        //Steps
        $attributeIndex->open();
        $attributeIndex->getPageActionsBlock()->addNew();
        $attributeNew->getAttributeForm()->fill($attribute);
        $attributeNew->getPageActions()->save();
    }
}

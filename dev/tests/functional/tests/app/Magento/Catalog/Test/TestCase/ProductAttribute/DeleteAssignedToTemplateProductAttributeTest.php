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
 * Test Creation for Delete Attribute Set (Product Template)
 *
 * Preconditions:
 * 1. Create Product template, based on Default
 * 2. Create product attribute and add to created template
 *
 * Test Flow:
 * 1. Log in to Backend.
 * 2. Go to Stores > Attributes > Product
 * 3. Search product attribute in grid by given data
 * 4. Open this attribute by clicking
 * 5. Click on the "Delete Attribute" button
 * 6. Perform all assertions.
 *
 * @group Product_Attributes_(MX)
 * @ZephyrId MAGETWO-26011
 */
class DeleteAssignedToTemplateProductAttributeTest extends Injectable
{
    /**
     * Catalog Product Attribute index page
     *
     * @var CatalogProductAttributeIndex
     */
    protected $attributeIndex;

    /**
     * Catalog Product Attribute new page
     *
     * @var CatalogProductAttributeNew
     */
    protected $attributeNew;

    /**
     * Inject pages
     *
     * @param CatalogProductAttributeIndex $attributeIndex
     * @param CatalogProductAttributeNew $attributeNew
     * @return void
     */
    public function __inject(CatalogProductAttributeIndex $attributeIndex, CatalogProductAttributeNew $attributeNew)
    {
        $this->attributeIndex = $attributeIndex;
        $this->attributeNew = $attributeNew;
    }

    /**
     * Run DeleteAssignedToTemplateProductAttribute test
     *
     * @param CatalogAttributeSet $productTemplate
     * @return array
     */
    public function test(CatalogAttributeSet $productTemplate)
    {
        // Precondition
        $productTemplate->persist();

        // Steps
        $filter = [
            'attribute_code' => $productTemplate
                ->getDataFieldConfig('assigned_attributes')['source']
                ->getAttributes()[0]
                ->getAttributeCode()
        ];

        $this->attributeIndex->open();
        $this->attributeIndex->getGrid()->searchAndOpen($filter);
        $this->attributeNew->getPageActions()->delete();

        return ['productTemplate' => $productTemplate];
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;

/**
 * Class AssertAbsenceProductAttributeInGrid
 * Checks that product attribute cannot be found by attribute code
 */
class AssertProductAttributeAbsenceInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after deleted product attribute cannot be found by attribute code.
     *
     * @param CatalogProductAttributeIndex $attributeIndex
     * @param CatalogProductAttribute $attribute
     * @return void
     */
    public function processAssert(
        CatalogProductAttributeIndex $attributeIndex,
        CatalogProductAttribute $attribute
    ) {
        $filter = [
            'attribute_code' => $attribute->getAttributeCode(),
        ];

        $attributeIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $attributeIndex->getGrid()->isRowVisible($filter),
            'Attribute \'' . $attribute->getFrontendLabel() . '\' is present in Attribute grid.'
        );
    }

    /**
     * Text absent Product Attribute in Attribute Grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Product Attribute is absent in Attribute grid.';
    }
}

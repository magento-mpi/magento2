<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeIndex;

/**
 * Class AssertProductAttributeInGrid
 * Assert that created product attribute is found in grid
 */
class AssertProductAttributeInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created product attribute is found in grid
     *
     * @param CatalogProductAttribute $attribute
     * @param CatalogProductAttributeIndex $attributeIndexPage
     * @return void
     */
    public function processAssert(CatalogProductAttribute $attribute, CatalogProductAttributeIndex $attributeIndexPage)
    {
        $attributeIndexPage->open();
        $code = $attribute->getAttributeCode();
        \PHPUnit_Framework_Assert::assertTrue(
            $attributeIndexPage->getGrid()->isRowVisible(['attribute_code' => $code]),
            'Attribute with attribute code "' . $code . '" is absent in attribute grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product attribute is present in attribute grid.';
    }
}

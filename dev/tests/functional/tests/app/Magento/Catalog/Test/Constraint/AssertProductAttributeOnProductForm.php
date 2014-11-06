<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;

/**
 * Assert that attribute is present on product form.
 */
class AssertProductAttributeOnProductForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that attribute is present on product form.
     *
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductEdit $catalogProductEdit
     * @param InjectableFixture $product
     * @param CatalogProductAttribute $attribute
     * @return void
     */
    public function processAssert(
        CatalogProductIndex $catalogProductIndex,
        CatalogProductEdit $catalogProductEdit,
        InjectableFixture $product,
        CatalogProductAttribute $attribute
    ) {
        $catalogProductIndex->open()->getProductGrid()->searchAndOpen(['sku' => $product->getSku()]);
        $attributeForm = $catalogProductEdit->getProductForm()->getCustomAttributeBlock($attribute);

        \PHPUnit_Framework_Assert::assertTrue($attributeForm->isVisible(), 'Attribute is not visible on product page.');
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute is visible on product page.';
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;

/**
 * Check whether the attribute mandatory.
 */
class AssertProductAttributeIsRequired extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Check whether the attribute mandatory.
     *
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductEdit $catalogProductEdit
     * @param CatalogProductAttribute $attribute
     * @param InjectableFixture|null $product
     * @return void
     */
    public function processAssert(
        CatalogProductIndex $catalogProductIndex,
        CatalogProductEdit $catalogProductEdit,
        CatalogProductAttribute $attribute,
        InjectableFixture $product
    ) {
        $catalogProductIndex->open()->getProductGrid()->searchAndOpen(['sku' => $product->getSku()]);
        $catalogProductEdit->getProductForm()->getCustomAttributeBlock($attribute)->setValue();
        $catalogProductEdit->getFormPageActions()->save();
        $failedAttributes = $catalogProductEdit->getProductForm()->getRequireNoticeAttributes();

        \PHPUnit_Framework_Assert::assertEmpty($failedAttributes, 'JS error notice is visible on product edit page.');
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return '"This is a required field" notice is not visible on product edit page.';
    }
}

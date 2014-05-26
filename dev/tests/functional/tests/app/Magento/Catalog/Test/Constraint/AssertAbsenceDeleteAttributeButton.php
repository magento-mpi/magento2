<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeNew;

/**
 * Class AssertAbsenceDeleteAttributeButton
 * Checks the button "Delete Attribute" on the Attribute page
 */
class AssertAbsenceDeleteAttributeButton extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that Delete Attribute button is absent for system attribute on attribute edit page.
     *
     * @param CatalogProductAttributeNew $attributeNew
     * @return void
     */
    public function processAssert(CatalogProductAttributeNew $attributeNew)
    {
        \PHPUnit_Framework_Assert::assertFalse(
            $attributeNew->getPageActions()->checkDeleteAttributeButton(),
            "Button 'Delete Attribute' is present on Attribute page"
        );
    }

    /**
     * Text absent button "Delete Attribute" on the Attribute page
     *
     * @return string
     */
    public function toString()
    {
        return "Button 'Delete Attribute' is absent on Attribute Page.";
    }
}

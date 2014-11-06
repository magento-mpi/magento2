<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\CatalogSearch\Test\Page\AdvancedSearch;
use Mtf\Constraint\AbstractConstraint;

/**
 * Check whether attribute displayed in the advanced search form on the frontend.
 */
class AssertProductAttributeDisplayingOnSearchForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Check whether attribute displayed in the advanced search form on the frontend.
     *
     * @param CatalogProductAttribute $attribute
     * @param AdvancedSearch $advancedSearch
     * @return void
     */
    public function processAssert(CatalogProductAttribute $attribute, AdvancedSearch $advancedSearch)
    {
        $advancedSearch->open();
        $formFields = $advancedSearch->getForm()->getFormFields();
        $label = $attribute->hasData('manage_frontend_label')
            ? $attribute->getManageFrontendLabel()
            : $attribute->getFrontendLabel();
        \PHPUnit_Framework_Assert::assertTrue(
            in_array($label, array_keys($formFields)),
            'Attribute is absent on advanced search form.'
        );
    }

    /**
     * Returns string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute is present on advanced search form.';
    }
}

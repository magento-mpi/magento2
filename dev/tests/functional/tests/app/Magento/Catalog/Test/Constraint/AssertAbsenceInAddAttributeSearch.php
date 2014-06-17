<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertAbsenceInAddAttributeSearch
 * Checks that product attribute cannot be added to product template on Product Page via Add Attribute control
 */
class AssertAbsenceInAddAttributeSearch extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Attribute frontend label
     *
     * @var string
     */
    protected $attributeFrontendLabel;

    /**
     * Assert that deleted attribute can't be added to product template on Product Page via Add Attribute control
     *
     * @param CatalogProductAttribute $productAttribute
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductNew $newProductPage
     * @return void
     */
    public function processAssert
    (
        CatalogProductAttribute $productAttribute,
        CatalogProductIndex $productGrid,
        CatalogProductNew $newProductPage
    ) {
        $this->attributeFrontendLabel = $productAttribute->getFrontendLabel();

        $productGrid->open();
        $productGrid->getProductBlock()->addProduct('configurable');
        $newProductPage->getForm()->addAttribute();
        $newProductPage->getSearchAttributeForm()->fillSearch($this->attributeFrontendLabel);

        \PHPUnit_Framework_Assert::assertNotEquals(
            $this->attributeFrontendLabel,
            $newProductPage->getSearchAttributeForm()->getSearchAttribute(),
            'Product attribute not found.'
            . "\nExpected: " . $this->attributeFrontendLabel
            . "\nActual: " . $newProductPage->getSearchAttributeForm()->getSearchAttribute()
        );
    }

    /**
     * Text absent Product Attribute in Attribute Search form
     *
     * @return string
     */
    public function toString()
    {
        return "Product Attribute is absent in Attribute Search form.";
    }
}

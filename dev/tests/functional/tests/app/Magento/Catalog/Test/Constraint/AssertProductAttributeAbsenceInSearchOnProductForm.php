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
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

/**
 * Class AssertAbsenceInAddAttributeSearch
 * Checks that product attribute cannot be added to product template on Product Page via Add Attribute control
 */
class AssertProductAttributeAbsenceInSearchOnProductForm extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const NO_FOUND_MESSAGE = 'No records found.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

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
        $productGrid->open();
        $productGrid->getProductBlock()->addProduct('simple');
        $message = $newProductPage->getForm()->isExistAttributeInSearchResult($productAttribute->getFrontendLabel());
        \PHPUnit_Framework_Assert::assertEquals(
            self::NO_FOUND_MESSAGE,
            $message,
            'Product attribute found in Attribute Search form.'
            . "\nExpected: " . self::NO_FOUND_MESSAGE
            . "\nActual: " . $message
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

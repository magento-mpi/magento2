<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductTemplateForm
 * Checking data from Product Template form with data fixture
 */
class AssertProductTemplateForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after save a product template on edit product set page displays:
     * 1. Correct product template name in 'attribute_set_name' field passed from fixture
     * 2. Created Product Attribute (if was added)
     *
     * @param CatalogProductSetIndex $productSet
     * @param CatalogProductSetEdit $productSetEdit
     * @param CatalogAttributeSet $attributeSet
     * @param CatalogProductAttribute $productAttribute
     * @return void
     */
    public function processAssert
    (
        CatalogProductSetIndex $productSet,
        CatalogProductSetEdit $productSetEdit,
        CatalogAttributeSet $attributeSet,
        CatalogProductAttribute $productAttribute
    ) {
        $filterAttribute = [
            'set_name' => $attributeSet->getAttributeSetName(),
        ];
        $productSet->open();
        $productSet->getGrid()->searchAndOpen($filterAttribute);
        \PHPUnit_Framework_Assert::assertEquals(
            $filterAttribute['set_name'],
            $productSetEdit->getMain()->getAttributeSetName(),
            'Attribute Set not found'
            . "\nExpected: " .  $filterAttribute['set_name']
            . "\nActual: " . $productSetEdit->getMain()->getAttributeSetName()
        );

        $attributeLabel = $productAttribute->getFrontendLabel();
        \PHPUnit_Framework_Assert::assertTrue(
            $productSetEdit->getMain()->checkProductAttribute($attributeLabel),
            "Product Attribute is absent on Product Template Groups"
        );
    }

    /**
     * Text matches the data from a form with data from fixture
     *
     * @return string
     */
    public function toString()
    {
        return 'Data from the Product Template form matched with fixture';
    }
}

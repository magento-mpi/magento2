<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureFactory;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Class AssertProductTemplateOnProductForm
 * Check Attribute Set and Product Attribute on Product form
 */
class AssertProductTemplateOnProductForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that created product template:
     * 1. Displays in product template suggest container dropdown
     * 2. Can be used for new created product.
     *
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductEdit $productEdit
     * @param CatalogProductIndex $productGrid
     * @param CatalogAttributeSet $attributeSet
     * @param CatalogProductNew $newProductPage
     * @param CatalogProductAttribute $productAttribute
     * @return void
     */
    public function processAssert
    (
        FixtureFactory $fixtureFactory,
        CatalogProductEdit $productEdit,
        CatalogProductIndex $productGrid,
        CatalogAttributeSet $attributeSet,
        CatalogProductNew $newProductPage,
        CatalogProductAttribute $productAttribute
    ) {

        $productGrid->open();
        $productGrid->getGridPageActionBlock()->addProduct('simple');
        $productBlockForm = $newProductPage->getForm();

        /**@var CatalogProductSimple $catalogProductSimple */
        $productSimple = $fixtureFactory->createByCode(
            'catalogProductSimple',
            [
                'dataSet' => 'default',
                'data' => [
                    'attribute_set_id' => ['attribute_set' => $attributeSet],
                ],
            ]
        );
        $productBlockForm->fillProduct($productSimple);
        $newProductPage->getFormAction()->save();

        $formData = $productEdit->getForm()->getData($productSimple);
        $formAttributeSet = $formData['attribute_set_id'];
        \PHPUnit_Framework_Assert::assertEquals(
            $attributeSet->getAttributeSetName(),
            $formAttributeSet,
            'Attribute Set not found on Product form.'
            . "\nExpected: " . $attributeSet->getAttributeSetName()
            . "\nActual: " . $formAttributeSet
        );

        $productEdit->getForm()->openTab('product-details');
        \PHPUnit_Framework_Assert::assertTrue(
            $productEdit->getForm()->checkAttributeLabel($productAttribute),
            "Product Attribute is absent on Product form."
        );
    }

    /**
     * Text of Product Attribute and Attribute Set are present on the Product form.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product Attribute and Attribute Set are present on the Product form.';
    }
}

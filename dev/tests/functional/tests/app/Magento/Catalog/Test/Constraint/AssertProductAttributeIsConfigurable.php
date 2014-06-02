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
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;
use Magento\ConfigurableProduct\Test\Page\Adminhtml\CatalogProductNew;

/**
 * Class AssertProductAttributeIsConfigurable
 */
class AssertProductAttributeIsConfigurable extends AbstractConstraint
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
     * Assert check whether the attribute is used to create a configurable products
     *
     * @param CatalogProductAttribute $productAttribute
     * @param CatalogProductIndex $productGrid
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductNew $newProductPage
     */
    public function processAssert
    (
        CatalogProductAttribute $productAttribute,
        CatalogProductIndex $productGrid,
        FixtureFactory $fixtureFactory,
        CatalogProductNew $newProductPage
    ) {
        $this->attributeFrontendLabel = $productAttribute->getFrontendLabel();
        $productGrid->open();
        $productGrid->getProductBlock()->addProduct('configurable');

        $productConfigurable = $fixtureFactory->createByCode(
            'catalogProductConfigurable',
            [
                'dataSet' => 'default',
                'data' => [
                    'configurable_attributes_data' => [
                        'value' => [
                            'label' => [
                                'value' => $this->attributeFrontendLabel
                            ]
                        ]
                    ]
                ],
            ]
        );

        $productBlockForm = $newProductPage->getForm();
        $productBlockForm->fill($productConfigurable);

        \PHPUnit_Framework_Assert::assertEquals(
            $this->attributeFrontendLabel,
            $newProductPage->getForm()->findAttribute(),
            'Product attribute not found.'
            . "\nExpected: " . $this->attributeFrontendLabel
            . "\nActual: " . $newProductPage->getForm()->findAttribute()
        );
    }

    /**
     * Attribute '$this->attributeFrontendLabel' present on the product page in variations section
     *
     * @return string
     */
    public function toString()
    {
        return "$this->attributeFrontendLabel attribute present on the product page in variations section";
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Fixture\FixtureFactory;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;

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
     * @var CatalogProductAttribute
     */
    protected $attribute;

    /**
     * Assert check whether the attribute is used to create a configurable products
     *
     * @param CatalogProductAttribute $productAttribute
     * @param CatalogProductAttribute $attribute
     * @param CatalogProductIndex $productGrid
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductNew $newProductPage
     */
    public function processAssert
    (
        CatalogProductAttribute $attribute,
        CatalogProductIndex $productGrid,
        FixtureFactory $fixtureFactory,
        CatalogProductNew $newProductPage,
        CatalogProductAttribute $productAttribute = null
    ) {
        $this->attribute = !is_null($productAttribute) ? $productAttribute : $attribute;
        $productGrid->open();
        $productGrid->getGridPageActionBlock()->addProduct('configurable');

        $productConfigurable = $fixtureFactory->createByCode(
            'catalogProductConfigurable',
            [
                'dataSet' => 'default',
                'data' => [
                    'configurable_attributes_data' => [
                        'preset' => 'one_variation',
                        'attributes' => [
                            $this->attribute
                        ]
                    ]
                ],
            ]
        );

        $productBlockForm = $newProductPage->getForm();
        $productBlockForm->fill($productConfigurable);

        \PHPUnit_Framework_Assert::assertTrue(
            $newProductPage->getForm()->findAttribute($this->attribute->getFrontendLabel()),
            "Product attribute is absent on the product page."
        );
    }

    /**
     * Attribute label present on the product page in variations section
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute label, present on the product page in variations section.';
    }
}

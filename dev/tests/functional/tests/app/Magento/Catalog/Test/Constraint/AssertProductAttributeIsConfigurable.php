<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint; 

use Magento\Catalog\Test\Fixture\CatalogAttributeEntity;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductAttributeIsConfigurable
 *
 * @package Magento\Catalog\Test\Constraint
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
     * @param CatalogAttributeEntity $productAttribute
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductNew $newProductPage
     * @return void
     */
    public function processAssert
    (
        CatalogAttributeEntity $productAttribute,
        CatalogProductIndex $productGrid,
        CatalogProductNew $newProductPage
    ) {
        $this->attributeFrontendLabel = $productAttribute->getFrontendLabel();

        $productGrid->open();
        $productGrid->getProductBlock()->addProduct('configurable');
        $newProductPage->getForm()->clickAddAttribute();
        $searchForm = $newProductPage->getSearchAttributeForm();
        $searchForm->fillSearch($this->attributeFrontendLabel);

        \PHPUnit_Framework_Assert::assertEquals(
            $this->attributeFrontendLabel,
            $searchForm->getSearchAttribute(),
            'Product attribute not found.'
            . "\nExpected: " . $this->attributeFrontendLabel
            . "\nActual: " . $searchForm->getSearchAttribute()
        );
    }

    /**
     * Attribute '$this->attributeFrontendLabel' present on the product page after search
     *
     * @return string
     */
    public function toString()
    {
        return "$this->attributeFrontendLabel status is present.";
    }
}

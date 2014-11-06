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
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config as TabVariation;

/**
 * Class AssertProductAttributeIsConfigurable
 * Assert check whether the attribute is used to create a configurable products
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
     * Assert check whether the attribute is used to create a configurable products
     *
     * @param CatalogProductAttribute $productAttribute
     * @param CatalogProductAttribute $attribute
     * @param CatalogProductIndex $productGrid
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductNew $newProductPage
     */
    public function processAssert(
        CatalogProductAttribute $attribute,
        CatalogProductIndex $productGrid,
        FixtureFactory $fixtureFactory,
        CatalogProductNew $newProductPage,
        CatalogProductAttribute $productAttribute = null
    ) {
        $attributeSearch = is_null($productAttribute) ? $attribute : $productAttribute;
        $productGrid->open();
        $productGrid->getGridPageActionBlock()->addProduct('configurable');
        $productBlockForm = $newProductPage->getProductForm();
        $productBlockForm->openTab('variations');

        /** @var TabVariation $tabVariation */
        $tabVariation = $productBlockForm->getTabElement('variations');
        $configurableAttributeSelector = $tabVariation->getAttributeBlock()->getAttributeSelector();
        \PHPUnit_Framework_Assert::assertTrue(
            $configurableAttributeSelector->isExistAttributeInSearchResult($attributeSearch),
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
        return 'Attribute label present on the product page in variations section.';
    }
}

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
use Magento\Catalog\Test\Fixture\CatalogProductTemplate;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;

/**
 * Class AssertProductAttributeOnProductForm
 */
class AssertProductAttributeOnProductForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Add this attribute to Default attribute Template. Create product and Assert that created attribute
     * is displayed on product form (Products > Inventory > Catalog)
     *
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductSetIndex $productSet
     * @param CatalogProductSetEdit $productSetEdit
     * @param CatalogProductTemplate $productTemplate
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductAttribute $attribute
     * @param CatalogProductAttribute $productAttribute
     * @param CatalogProductEdit $productEdit
     * @param $product
     * @return void
     */
    public function processAssert
    (
        FixtureFactory $fixtureFactory,
        CatalogProductSetIndex $productSet,
        CatalogProductSetEdit $productSetEdit,
        CatalogProductTemplate $productTemplate,
        CatalogProductIndex $productGrid,
        CatalogProductAttribute $attribute,
        CatalogProductAttribute $productAttribute,
        CatalogProductEdit $productEdit,
        $product
    ) {
        $filterAttribute = [
            'set_name' => $productTemplate->getAttributeSetName(),
        ];
        $productSet->open();
        $productSet->getBlockAttributeSetGrid()->searchAndOpen($filterAttribute);
        $productSetEdit->getNewAttributes()->moveAttribute($attribute->getFrontendLabel());
        $productSetEdit->getPageActions()->save();

        $product = explode('::', $product);
        $product = $fixtureFactory->createByCode(
            $product[0],
            [
                'dataSet' => $product[1],
                'data' => [
                    'attribute_set_id' => $productTemplate->getData('id'),
                    'configurable_attributes_data' => [
                        $attribute->getData('id') => [
                            'attribute_id' => $attribute->getData('id'),
                            'code' => $attribute->getData('frontend_label'),
                            'label' => $attribute->getData('frontend_label'),
                            'id' => 'new',
                        ]
                    ]
                ]
            ]
        );
        $product->persist();

        $filterProduct = [
            'sku' => $product->getSku(),
        ];
        $productGrid->open();
        $productGrid->getProductGrid()->searchAndOpen($filterProduct);

        \PHPUnit_Framework_Assert::assertTrue(
            $productEdit->getForm()->checkAttributeLabel($productAttribute->getFrontendLabel()),
            "Product Attribute is absent on Product form."
        );
    }

    /**
     * Text of Product Attribute is present on the Product form.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product Attribute is present on Product form.';
    }
}

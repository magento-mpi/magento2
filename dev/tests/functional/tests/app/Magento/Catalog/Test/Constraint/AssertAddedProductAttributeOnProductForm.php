<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureFactory;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;

/**
 * Class AssertAddedProductAttributeOnProductForm
 * Check attribute on product form
 */
class AssertAddedProductAttributeOnProductForm extends AbstractConstraint
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
     * @param CatalogAttributeSet $productTemplate
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductAttribute $productAttributeOriginal
     * @param CatalogProductEdit $productEdit
     * @param CatalogProductAttribute|null $productAttribute
     * @return void
     */
    public function processAssert
    (
        FixtureFactory $fixtureFactory,
        CatalogProductSetIndex $productSet,
        CatalogProductSetEdit $productSetEdit,
        CatalogAttributeSet $productTemplate,
        CatalogProductIndex $productGrid,
        CatalogProductEdit $productEdit,
        CatalogProductAttribute $productAttribute,
        CatalogProductAttribute $productAttributeOriginal = null
    ) {
        $filterAttribute = [
            'set_name' => $productTemplate->getAttributeSetName(),
        ];
        $productSet->open();
        $productSet->getGrid()->searchAndOpen($filterAttribute);

        $attributeData = ($productAttributeOriginal !== null)
            ? array_merge($productAttribute->getData(), $productAttributeOriginal->getData())
            : $productAttribute->getData();

        $productSetEdit->getMain()->moveAttribute($attributeData, 'Product Details');
        $productSetEdit->getPageActions()->save();

        $product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            [
                'dataSet' => 'product_with_category',
                'data' => [
                    'attribute_set_id' => ['attribute_set' => $productTemplate],
                ],
            ]
        );
        $product->persist();

        $filterProduct = [
            'sku' => $product->getSku(),
        ];
        $productGrid->open();
        $productGrid->getProductGrid()->searchAndOpen($filterProduct);

        $frontendLabel = ($productAttributeOriginal !== null)
            ? array_merge($productAttributeOriginal->getData(), $productAttribute->getData())['frontend_label']
            : $productAttribute->getData()['frontend_label'];

        \PHPUnit_Framework_Assert::assertTrue(
            $productEdit->getForm()->checkAttributeLabel($frontendLabel),
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

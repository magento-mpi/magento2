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
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureFactory;

/**
 * Check whether the attribute unique.
 */
class AssertProductAttributeIsUnique extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Check whether the attribute unique.
     *
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductEdit $catalogProductEdit
     * @param CatalogProductSimple $productSimple
     * @param CatalogProductAttribute $attribute
     * @param FixtureFactory $fixtureFactory
     * @param CatalogAttributeSet|null $productTemplate
     * @throws \Exception
     * @return void
     */
    public function processAssert(
        CatalogProductIndex $catalogProductIndex,
        CatalogProductEdit $catalogProductEdit,
        CatalogProductSimple $productSimple,
        CatalogProductAttribute $attribute,
        FixtureFactory $fixtureFactory,
        CatalogAttributeSet $productTemplate = null
    ) {
        if ($productTemplate !== null) {
            $productSimple = $fixtureFactory->createByCode(
                'catalogProductSimple',
                [
                    'dataSet' => 'product_with_category_with_anchor',
                    'data' => [
                        'attribute_set_id' => ['attribute_set' => $productTemplate],
                    ],
                ]
            );
        }
        $productSimple->persist();

        $productForm = $catalogProductEdit->getProductForm();
        $catalogProductIndex->open()->getProductGrid()->searchAndOpen(['sku' => $productSimple->getSku()]);
        $productForm->getCustomAttributeBlock($attribute)->setAttributeValue();

        \PHPUnit_Framework_Assert::assertTrue(
            $productForm->getCustomAttributeBlock($attribute)->getRequireNotice(),
            'Attribute is not unique.'
        );
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute is unique.';
    }
}

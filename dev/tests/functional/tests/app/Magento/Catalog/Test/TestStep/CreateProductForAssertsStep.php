<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestStep;

use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestStep\TestStepInterface;

/**
 * Create Product For Asserts using handler.
 */
class CreateProductForAssertsStep implements TestStepInterface
{
    /**
     * Factory for Fixtures.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * CatalogAttributeSet fixture.
     *
     * @var CatalogAttributeSet
     */
    protected $productTemplate;

    /**
     * CatalogProductAttribute fixture.
     *
     * @var CatalogProductAttribute
     */
    protected $attribute;

    /**
     * Preparing step properties.
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param CatalogAttributeSet $productTemplate
     * @param CatalogProductAttribute $attribute
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        CatalogAttributeSet $productTemplate,
        CatalogProductAttribute $attribute
    ) {
        $this->fixtureFactory = $fixtureFactory;
        $this->productTemplate = $productTemplate;
        $this->attribute = $attribute;
    }

    /**
     * Create Product For Asserts using handler.
     *
     * @return array
     */
    public function run()
    {
        $product = $this->fixtureFactory->createByCode(
            'catalogProductSimple',
            [
                'dataSet' => 'product_with_category_with_anchor',
                'data' => [
                    'attribute_set_id' => ['attribute_set' => $this->productTemplate],
                    'custom_attribute' => $this->attribute
                ],
            ]
        );
        $product->persist();
        return ['product' => $product];
    }
}

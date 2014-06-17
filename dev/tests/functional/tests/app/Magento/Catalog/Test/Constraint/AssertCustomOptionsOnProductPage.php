<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertCustomOptionsOnProductPage
 */
class AssertCustomOptionsOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Skipped field for custom options
     *
     * @var array
     */
    protected $skippedFieldOptions = [
        'Field' => [
            'price_type',
            'sku',
        ],
        'Drop-down' => [
            'price_type',
            'sku',
            'price',
        ]
    ];

    /**
     * Assertion that commodity options are displayed correctly
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductSimple $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, CatalogProductSimple $product)
    {
        // TODO fix initialization url for frontend page
        // Open product view page
        $catalogProductView->init($product);
        $catalogProductView->open();
        // Prepare data
        $formCustomOptions = $catalogProductView->getCustomOptionsBlock()->getOptions($product);
        $fixtureCustomOptions = $this->prepareOptions($product->getCustomOptions());

        \PHPUnit_Framework_Assert::assertEquals(
            $formCustomOptions,
            $fixtureCustomOptions,
            'Incorrect display of custom product options on the product page.'
        );
    }

    /**
     * Preparation options before comparing
     *
     * @param array $customOptions
     * @return array
     */
    protected function prepareOptions(array $customOptions)
    {
        $result = [];
        foreach ($customOptions as $customOption) {
            $skippedField = $this->skippedFieldOptions[$customOption['type']];
            foreach ($customOption['options'] as &$option) {
                $option = array_diff_key($option, array_flip($skippedField));
            }

            $result[$customOption['title']] = $customOption;
        }

        return $result;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Value of custom option on the page is correct.';
    }
}

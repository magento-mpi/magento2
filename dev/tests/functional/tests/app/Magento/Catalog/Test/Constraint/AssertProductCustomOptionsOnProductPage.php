<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductCustomOptionsOnProductPage
 */
class AssertProductCustomOptionsOnProductPage extends AbstractAssertForm
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
        'Area' => [
            'price_type',
            'sku',
        ],
        'Drop-down' => [
            'price_type',
            'sku',
        ],
        'File' => [
            'price_type',
            'sku',
        ],
        'Radio Buttons' => [
            'price_type',
            'sku',
        ],
        'Checkbox' => [
            'price_type',
            'sku',
        ],
        'Multiple Select' => [
            'price_type',
            'sku',
        ],
        'Date' => [
            'price_type',
            'sku',
        ],
        'Date & Time' => [
            'price_type',
            'sku',
        ],
        'Time' => [
            'price_type',
            'sku',
        ]
    ];

    /**
     * Assertion that commodity options are displayed correctly
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product)
    {
        // TODO fix initialization url for frontend page
        // Open product view page
        $catalogProductView->init($product);
        $catalogProductView->open();
        // Prepare data
        $formCustomOptions = $catalogProductView->getCustomOptionsBlock()->getOptions($product);
        $prices = $catalogProductView->getViewBlock()->getProductPriceBlock()->getPrice();
        $actualPrice = isset($prices['price_special_price'])
            ? $prices['price_special_price']
            : $prices['price_regular_price'];
        $fixtureCustomOptions = $this->prepareOptions($product, $actualPrice);
        $error = $this->verifyData($fixtureCustomOptions, $formCustomOptions);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Preparation options before comparing
     *
     * @param FixtureInterface $product
     * @param int|null $actualPrice
     * @return array
     */
    protected function prepareOptions(FixtureInterface $product, $actualPrice = null)
    {
        $customOptions = $product->getCustomOptions();
        $result = [];

        $actualPrice = $actualPrice ? $actualPrice : $product->getPrice();
        foreach ($customOptions as $customOption) {
            $skippedField = isset($this->skippedFieldOptions[$customOption['type']])
                ? $this->skippedFieldOptions[$customOption['type']]
                : [];
            foreach ($customOption['options'] as &$option) {
                // recalculate percent price
                if ('Percent' == $option['price_type']) {
                    $option['price'] = ($actualPrice * $option['price']) / 100;
                    $option['price'] = round($option['price'], 2);
                }

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

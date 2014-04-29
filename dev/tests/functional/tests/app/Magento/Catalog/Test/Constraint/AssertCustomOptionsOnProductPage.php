<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\InjectableFixture;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertCustomOptionsOnProductPage
 *
 * @package Magento\Catalog\Test\Constraint
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
     * Product page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Product fixture
     *
     * @var InjectableFixture
     */
    protected $product;

    /**
     * @param CatalogProductView $catalogProductView
     * @param InjectableFixture $product
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        InjectableFixture $product
    ) {
        // TODO fix initialization url for frontend page
        // Open product view page
        $catalogProductView->init($product);
        $catalogProductView->open();
        $customOptions = $catalogProductView->getCustomOptions()->getOptions();

        $compareOptions = $product->getCustomOptions();
        $noError = true;
        $requireCompere = ['Yes' => true, 'No' => false];
        foreach ($compareOptions as $compareOption) {
            if (!$noError) {
                break;
            }
            foreach ($customOptions as &$optionOnPage) {
                if ($noError = $optionOnPage['title'] === $compareOption['title']
                    && !empty($compareOption['options']) && !empty($optionOnPage['price'])
                    && $requireCompere[$compareOption['is_require']] === $optionOnPage['is_require']
                ) {
                    foreach ($compareOption['options'] as $optionVariant) {
                        if ($optionVariant['price_type'] === 'Percent') {
                            $optionVariant['price'] = $product->getData('price') / 100 * $optionVariant['price'];
                        }
                        if ($noError = in_array(number_format($optionVariant['price'], 2), $optionOnPage['price'])) {
                            unset($optionOnPage);
                            break;
                        }
                    }
                    break;
                }
            }
        }
        unset($optionOnPage);
        \PHPUnit_Framework_Assert::assertTrue(
            $noError,
            'Incorrect display of custom product options on the product page.'
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Value of custom option on the page is not correct.';
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint; 

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
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
     * @var CatalogProductSimple
     */
    protected $product;

    /**
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductSimple $product
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductSimple $product
    ) {
        // TODO fix initialization url for frontend page
        // Open product view page
        $catalogProductView->init($product);
        $catalogProductView->open();
        $customOptions = $catalogProductView->getCustomOptions()->getOptions();

        $compareOptions = $product->getCustomOptions();
        $noError = true;
        foreach ($compareOptions as $compareOption) {
            if (!$noError) {
                break;
            }
            foreach ($customOptions as $optionOnPage) {
                if ($noError = $optionOnPage['title'] === $compareOption['title']
                    && !empty($compareOption['options']) && !empty($optionOnPage['price'])
                ) {
                    foreach ($compareOption['options'] as $optionVariant) {
                        if ($noError = in_array(number_format($optionVariant['price'], 2), $optionOnPage['price'])) {
                            break;
                        }
                    }
                    break;
                }
            }
        }

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

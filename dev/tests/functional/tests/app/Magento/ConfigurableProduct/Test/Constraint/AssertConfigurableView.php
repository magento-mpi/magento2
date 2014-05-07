<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

/**
 * Class AssertConfigurableView
 *
 * @package Magento\ConfigurableProduct\Test\Constraint
 */
class AssertConfigurableView extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert configurable product, corresponds to the product page
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductConfigurable $configurable
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductConfigurable $configurable
    ) {
        //Open product view page
        $catalogProductView->init($configurable);
        $catalogProductView->open();

        //Process assertions
        $this->assertOnProductView($configurable, $catalogProductView);
    }

    /**
     * Assert prices on the product view Page
     *
     * @param CatalogProductConfigurable $configurable
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    protected function assertOnProductView(
        CatalogProductConfigurable $configurable,
        CatalogProductView $catalogProductView
    ) {
        /** @var \Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable\Price $priceFixture */
        $priceFixture = $configurable->getDataFieldConfig('price')['fixture'];
        $pricePresetData = $priceFixture->getPreset();

        if (isset($pricePresetData['product_special_price'])) {
            $regularPrice = $catalogProductView->getViewBlock()->getProductPriceBlock()->getRegularPrice();
            \PHPUnit_Framework_Assert::assertEquals(
                $pricePresetData['product_price'],
                $regularPrice,
                'Product regular price on product view page is not correct.'
            );

            $specialPrice = $catalogProductView->getViewBlock()->getProductPriceBlock()->getSpecialPrice();
            \PHPUnit_Framework_Assert::assertEquals(
                $pricePresetData['product_special_price'],
                $specialPrice,
                'Product special price on product view page is not correct.'
            );
        } else {
            //Price verification
            $price = $catalogProductView->getViewBlock()
                ->getProductPriceBlock($configurable->getName())
                ->getPrice();
            \PHPUnit_Framework_Assert::assertEquals(
                '$' . $price['price_regular_price'],
                $pricePresetData['product_price'],
                'Product price on category page is not correct.'
            );
        }
    }

    /**
     * Text of Visible in category assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Product price on product view page is not correct.';
    }
}

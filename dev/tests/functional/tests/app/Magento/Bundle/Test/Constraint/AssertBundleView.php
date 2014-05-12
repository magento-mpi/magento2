<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductView
 */
class AssertBundleView extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductBundle $bundle
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductBundle $bundle
    ) {
        //Open product view page
        $catalogProductView->init($bundle);
        $catalogProductView->open();

        //Process assertions
        $this->assertPrice($bundle, $catalogProductView);
    }

    /**
     * Assert prices on the product view Page
     *
     * @param CatalogProductBundle $bundle
     * @param CatalogProductView $catalogProductView
     */
    protected function assertPrice(CatalogProductBundle $bundle, CatalogProductView $catalogProductView)
    {
        /** @var \Magento\Catalog\Test\Fixture\CatalogProductSimple\Price $priceFixture */
        $priceFixture = $bundle->getDataFieldConfig('price')['source'];
        $pricePresetData = $priceFixture->getPreset();

        $priceBlock = $catalogProductView->getViewBlock()->getProductPriceBlock();
        \PHPUnit_Framework_Assert::assertEquals(
            $pricePresetData['price_from'],
            $priceBlock->getPriceFrom(),
            'Bundle price From on product view page is not correct.'
        );
        \PHPUnit_Framework_Assert::assertEquals(
            $pricePresetData['price_to'],
            $priceBlock->getPriceTo(),
            'Bundle price To on product view page is not correct.'
        );
    }

    /**
     * Text of Visible in category assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Bundle price on product view page is not correct.';
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Bundle\Test\Page\Product\CatalogProductView;
use Magento\Bundle\Test\Fixture\CatalogProductBundle;

/**
 * Class AssertBundlePriceView
 * Assert that displayed price view for bundle product on product page equals passed from fixture.
 */
class AssertBundlePriceView extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that displayed price view for bundle product on product page equals passed from fixture.
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductBundle $product
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductBundle $product
    ) {
        //Open product view page
        $catalogProductView->init($product);
        $catalogProductView->open();

        //Process assertions
        $this->assertPrice($product, $catalogProductView);
    }

    /**
     * Assert prices on the product view Page
     *
     * @param CatalogProductBundle $product
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    protected function assertPrice(CatalogProductBundle $product, CatalogProductView $catalogProductView)
    {
        $priceData = $product->getDataFieldConfig('price')['source']->getPreset();
        $priceBlock = $catalogProductView->getViewBlock()->getProductPriceBlock();

        \PHPUnit_Framework_Assert::assertEquals(
            $priceData['price_from'],
            $priceBlock->getPriceFrom(),
            'Bundle price From on product view page is not correct.'
        );
        \PHPUnit_Framework_Assert::assertEquals(
            $priceData['price_to'],
            $priceBlock->getPriceTo(),
            'Bundle price To on product view page is not correct.'
        );
    }

    /**
     * Text of Visible in bundle assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Bundle price on product view page is not correct.';
    }
}
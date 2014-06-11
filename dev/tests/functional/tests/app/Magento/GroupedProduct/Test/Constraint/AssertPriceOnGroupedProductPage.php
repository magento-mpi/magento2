<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Constraint;

use Mtf\Fixture\InjectableFixture;
use Mtf\Constraint\AbstractConstraint;
use Magento\GroupedProduct\Test\Fixture\CatalogProductGrouped;
use Magento\GroupedProduct\Test\Page\Product\CatalogProductView;

/**
 * Class AssertPriceOnGroupedProductPage
 * Assert that displayed price on grouped product page equals passed from fixture
 */
class AssertPriceOnGroupedProductPage
{
    /**
     * Constructor
     *
     * @constructor
     */
    public function __construct()
    {

    }

    /**
     * Verify product price on grouped product view page
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductGrouped $product
     * @param AbstractConstraint $object
     * @param string $typePrice
     * @return bool|string
     */
    public function assertPrice(
        CatalogProductGrouped $product,
        CatalogProductView $catalogProductView,
        AbstractConstraint $object,
        $typePrice = 'Special'
    ) {
        $catalogProductView->init($product);
        $catalogProductView->open();

        $groupedData = $product->getGroupedData();
        /** @var InjectableFixture $subProduct */
        foreach ($groupedData['products'] as $productIncrement => $subProduct) {
            //Process assertions
            $catalogProductView->getGroupedViewBlock()
                ->{'item' . ($typePrice != 'Tier' ? '' : 'Tier') . 'PriceProductBlock'}(++$productIncrement);
            $object->errMessage = sprintf($object->formatErrMessage, $subProduct->getData('name'));
            $object->{'assert' . $typePrice . 'Price'}($subProduct, $catalogProductView, 'GroupedView');
        }
    }
}

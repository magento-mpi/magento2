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
use Magento\Catalog\Test\Constraint\ConstrainPriceOnProductPageInterface;

/**
 * Class AssertPriceOnGroupedProductPageAbstract
 * Assert that displayed price on grouped product page equals passed from fixture
 */
abstract class AssertPriceOnGroupedProductPageAbstract extends AbstractConstraint
{
    /**
     * Format error message
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * Successful message
     *
     * @var string
     */
    protected $successfulMessage;

    /**
     * Tier price block
     *
     * @var string
     */
    protected $tierBlock;

    /**
     * Verify product price on grouped product view page
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductGrouped $product
     * @param ConstrainPriceOnProductPageInterface $object
     * @param string $typePrice
     * @return bool|string
     */
    public function processAssertPrice(
        CatalogProductGrouped $product,
        CatalogProductView $catalogProductView,
        ConstrainPriceOnProductPageInterface $object,
        $typePrice = ''
    ) {
        $catalogProductView->init($product);
        $catalogProductView->open();

        $groupedData = $product->getGroupedData();
        /** @var InjectableFixture $subProduct */
        foreach ($groupedData['products'] as $productIncrement => $subProduct) {
            //Process assertions
            $catalogProductView->getGroupedViewBlock()
                ->{'item' . $typePrice . 'PriceProductBlock'}(++$productIncrement);
            $object->setErrorMessage(sprintf($this->errorMessage, $subProduct->getData('name')));
            if($typePrice == 'Tier')
            {
                $object->setTierBlock($this->tierBlock);
            }
            $object->assertPrice($subProduct, $catalogProductView, 'Grouped');
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return $this->successfulMessage;
    }
}

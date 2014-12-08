<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Page\Product\CatalogProductCompare;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertProductIsNotVisibleInComparePage
 * Assert the product is not displayed on Compare Products page
 */
class AssertProductIsNotVisibleInComparePage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You have no items to compare.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert the product is not displayed on Compare Products page
     *
     * @param CatalogProductCompare $comparePage
     * @param FixtureInterface $product
     * @param int $countProducts [optional]
     * @return void
     */
    public function processAssert(CatalogProductCompare $comparePage, FixtureInterface $product, $countProducts = 0)
    {
        $comparePage->open();
        $compareBlock = $comparePage->getCompareProductsBlock();

        if ($countProducts > 1) {
            \PHPUnit_Framework_Assert::assertFalse(
                $compareBlock->isProductVisibleInCompareBlock($product->getName()),
                'The product displays on Compare Products page.'
            );
        } else {
            \PHPUnit_Framework_Assert::assertEquals(
                self::SUCCESS_MESSAGE,
                $compareBlock->getEmptyMessage(),
                'The product displays on Compare Products page.'
            );
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Products is not displayed on Compare Products page.';
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductCompare;

/**
 * Class AssertProductIsNotVisibleInComparePage
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
     * @param int $countProducts
     * @return void
     */
    public function processAssert(CatalogProductCompare $comparePage, FixtureInterface $product, $countProducts)
    {
        $comparePage->open();
        $name = $countProducts > 1 ? $product->getName() : '';
        $success = $name !== '' ? true : self::SUCCESS_MESSAGE;
        $actual = $comparePage->getCompareProductsBlock()->productIsNotInBlock($name);

        \PHPUnit_Framework_Assert::assertEquals($success, $actual, 'Wrong success message is displayed.');
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Products is not displayed on Compare Products page.';
    }
}

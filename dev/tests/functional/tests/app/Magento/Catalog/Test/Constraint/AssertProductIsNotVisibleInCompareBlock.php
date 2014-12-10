<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\CustomerAccountIndex;

/**
 * Class AssertProductIsNotVisibleInCompareBlock
 * Assert the product is not displayed on Compare Products block on my account page
 */
class AssertProductIsNotVisibleInCompareBlock extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    const SUCCESS_MESSAGE = 'You have no items to compare.';

    /**
     * Assert the product is not displayed on Compare Products block on my account page
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param int $countProducts [optional]
     * @param FixtureInterface $product [optional]
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        $countProducts = 0,
        FixtureInterface $product = null
    ) {
        $cmsIndex->open();
        $cmsIndex->getLinksBlock()->openLink("My Account");
        $compareBlock = $customerAccountIndex->getCompareProductsBlock();

        if (($countProducts > 1) && ($product !== null)) {
            \PHPUnit_Framework_Assert::assertFalse(
                $compareBlock->isProductVisibleInCompareBlock($product->getName()),
                'The product displays on Compare Products block on my account page.'
            );
        } else {
            \PHPUnit_Framework_Assert::assertEquals(
                self::SUCCESS_MESSAGE,
                $compareBlock->getEmptyMessage(),
                'The product displays on Compare Products block on my account page.'
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
        return 'The message appears on Compare Products block on my account page.';
    }
}

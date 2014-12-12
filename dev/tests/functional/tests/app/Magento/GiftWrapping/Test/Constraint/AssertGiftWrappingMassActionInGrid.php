<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftWrappingMassActionInGrid
 * Assert Gift Wrapping availability in Gift Wrapping grid after mass action
 */
class AssertGiftWrappingMassActionInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert Gift Wrapping availability in Gift Wrapping grid after mass action
     *
     * @param GiftWrappingIndex $giftWrappingIndexPage
     * @param array $giftWrappingsToStay
     * @param string $status
     * @param AssertGiftWrappingInGrid $assert
     * @return void
     */
    public function processAssert(
        GiftWrappingIndex $giftWrappingIndexPage,
        $giftWrappingsToStay,
        $status,
        AssertGiftWrappingInGrid $assert
    ) {
        foreach ($giftWrappingsToStay as $giftWrapping) {
            $assert->processAssert($giftWrappingIndexPage, $giftWrapping, $status);
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'All Gift Wrappings are present in grid.';
    }
}

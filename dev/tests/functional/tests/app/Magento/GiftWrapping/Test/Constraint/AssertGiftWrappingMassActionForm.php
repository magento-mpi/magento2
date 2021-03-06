<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingNew;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftWrappingMassActionForm
 * Assert that mass action Gift Wrapping form was filled correctly
 */
class AssertGiftWrappingMassActionForm extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * @param GiftWrappingIndex $giftWrappingIndexPage
     * @param GiftWrappingNew $giftWrappingNewPage
     * @param array $giftWrapping
     * @param string $status
     * @param AssertGiftWrappingForm $assert
     * @return void
     */
    public function processAssert(
        GiftWrappingIndex $giftWrappingIndexPage,
        GiftWrappingNew $giftWrappingNewPage,
        array $giftWrapping,
        $status,
        AssertGiftWrappingForm $assert
    ) {
        foreach ($giftWrapping as $item) {
            $assert->processAssert($giftWrappingIndexPage, $giftWrappingNewPage, $item, $status);
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'All Gift Wrapping forms were filled correctly.';
    }
}

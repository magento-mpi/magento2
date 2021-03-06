<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftWrappingNotInGrid
 * Assert that deleted Gift Wrapping can not be found in grid
 */
class AssertGiftWrappingNotInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that deleted Gift Wrapping can not be found in grid via: id, design, website_id, status, price
     *
     * @param GiftWrappingIndex $giftWrappingIndexPage
     * @param GiftWrapping $giftWrapping
     * @return void
     */
    public function processAssert(GiftWrappingIndex $giftWrappingIndexPage, $giftWrapping)
    {
        $data = $giftWrapping->getData();
        reset($data['website_ids']);
        $filter = [
            'wrapping_id_from' => $data['wrapping_id'],
            'wrapping_id_to' => $data['wrapping_id'],
            'design' => $data['design'],
            'status' => $data['status'],
            'website_ids' => current($data['website_ids']),
            'base_price' => $data['base_price'],
        ];

        $giftWrappingIndexPage->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $giftWrappingIndexPage->getGiftWrappingGrid()->isRowVisible($filter, true, false),
            'Gift Wrapping \'' . $filter['design'] . '\' is present in Gift Wrapping grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift Wrapping is not present in grid.';
    }
}

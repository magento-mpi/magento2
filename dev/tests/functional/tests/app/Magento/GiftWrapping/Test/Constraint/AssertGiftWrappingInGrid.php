<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftWrappingInGrid
 */
class AssertGiftWrappingInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert Gift Wrapping availability in Gift Wrapping grid
     *
     * @param GiftWrappingIndex $giftWrappingIndexPage
     * @param GiftWrapping $giftWrapping
     * @param GiftWrapping $initialGiftWrapping
     * @return void
     */
    public function processAssert(
        GiftWrappingIndex $giftWrappingIndexPage,
        GiftWrapping $giftWrapping,
        GiftWrapping $initialGiftWrapping = null
    ) {
        $data = ($initialGiftWrapping !== null)
            ? array_merge($initialGiftWrapping->getData(), $giftWrapping->getData())
            : $giftWrapping->getData();
        reset($data['website_ids']);
        $filter = [
            'design' => $data['design'],
            'status' => $data['status'],
            'website_ids' => current($data['website_ids']),
            'base_price' => $data['base_price'],
        ];

        $giftWrappingIndexPage->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $giftWrappingIndexPage->getGiftWrappingGrid()->isRowVisible($filter, true, false),
            'Gift Wrapping \'' . $filter['design'] . '\' is absent in Gift Wrapping grid.'
        );
    }

    /**
     * Text of Gift Wrapping in grid assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift Wrapping is present in grid.';
    }
}

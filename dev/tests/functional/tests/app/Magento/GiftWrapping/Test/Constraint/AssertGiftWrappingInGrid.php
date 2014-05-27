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
     * @param GiftWrapping $giftWrapping
     * @param GiftWrappingIndex $giftWrappingIndexPage
     * @return void
     */
    public function processAssert(
        GiftWrapping $giftWrapping,
        GiftWrappingIndex $giftWrappingIndexPage
    ) {
        $websites = $giftWrapping->getWebsiteIds();
        reset($websites);
        $filter = [
            'design' => $giftWrapping->getDesign(),
            'status' => $giftWrapping->getStatus(),
            'website_ids' => current($websites),
        ];

        $giftWrappingIndexPage->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $giftWrappingIndexPage->getGiftWrappingGrid()->isRowVisible($filter),
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

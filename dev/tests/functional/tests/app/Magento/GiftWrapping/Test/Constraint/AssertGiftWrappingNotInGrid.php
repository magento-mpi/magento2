<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;

/**
 * Class AssertGiftWrappingNotInGrid
 * Assert that deleted Gift Wrapping can not be found in grid
 */
class AssertGiftWrappingNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that deleted Gift Wrapping can not be found in grid via: id, design, website_id, status, price
     *
     * @param GiftWrappingIndex $giftWrappingIndexPage
     * @param array $giftWrappingsModified
     * @return void
     */
    public function processAssert(
        GiftWrappingIndex $giftWrappingIndexPage,
        array $giftWrappingsModified
    ) {
        $giftWrappingIndexPage->open();
        $errors = [];
        foreach ($giftWrappingsModified as $giftWrappingModified) {
            $data = $giftWrappingModified->getData();
            reset($data['website_ids']);
            $filter = [
                'wrapping_id_from' => $data['wrapping_id'],
                'wrapping_id_to' => $data['wrapping_id'],
                'design' => $data['design'],
                'status' => $data['status'],
                'website_ids' => current($data['website_ids']),
                'base_price' => $data['base_price'],
            ];
            if ($giftWrappingIndexPage->getGiftWrappingGrid()->isRowVisible($filter, true, false)) {
                $errors[] = '- row "' . implode(', ', $filter) . '" was found in gift wrapping grid';
            }
        }
        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            'Gift Wrapping is present in Gift Wrapping grid.'
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

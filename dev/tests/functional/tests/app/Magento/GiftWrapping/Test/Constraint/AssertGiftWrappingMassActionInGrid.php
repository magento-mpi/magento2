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
     * @param array $giftWrappings
     * @param string $status
     * @return void
     */
    public function processAssert(GiftWrappingIndex $giftWrappingIndexPage, array $giftWrappings, $status)
    {
        $giftWrappingIndexPage->open();
        $errors = [];
        foreach ($giftWrappings as $giftWrapping) {
            $data = $giftWrapping->getData();
            reset($data['website_ids']);
            $filter = [
                'design' => $data['design'],
                'status' => $status === '-' ? $data['status'] : $status,
                'website_ids' => current($data['website_ids']),
                'base_price' => $data['base_price'],
            ];
            if (!$giftWrappingIndexPage->getGiftWrappingGrid()->isRowVisible($filter, true, false)) {
                $errors[] = '- row "' . implode(', ', $filter) . '" was not found in gift wrapping grid';
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
        return 'Gift Wrapping is present in grid.';
    }
}

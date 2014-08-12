<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryIndex;
use Magento\GiftRegistry\TEst\Fixture\GiftRegistry;

/**
 * Class AssertGiftRegistryTypeInGrid
 * Assert that created Gift Registry type can be found at Stores > Gift Registry grid in backend
 */
class AssertGiftRegistryTypeInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created Gift Registry type can be found at Stores > Gift Registry grid in backend
     *
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistry $giftRegistry
     * @return void
     */
    public function processAssert(GiftRegistryIndex $giftRegistryIndex, GiftRegistry $giftRegistry)
    {
        $giftRegistryIndex->open();
        $filter = ['label' => $giftRegistry->getLabel()];
        \PHPUnit_Framework_Assert::assertTrue(
            $giftRegistryIndex->getGiftRegistryGrid()->isRowVisible($filter),
            'Gift registry \'' . $giftRegistry->getLabel() . '\' is not present in grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry type is present in GiftRegistryType grid.';
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;
use Magento\GiftRegistry\TEst\Fixture\GiftRegistry;

/**
 * Class AssertGiftRegistryNotInGrid
 * Assert that gift registry is absent in grid
 */
class AssertGiftRegistryNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created Gift Registry can not be found at Gift Registry grid by title
     *
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistry $giftRegistry
     * @return void
     */
    public function processAssert(GiftRegistryIndex $giftRegistryIndex, GiftRegistry $giftRegistry)
    {
        \PHPUnit_Framework_Assert::assertFalse(
            $giftRegistryIndex->open()->getGiftRegistryGrid()->isGiftRegistryInGrid($giftRegistry),
            'Gift registry \'' . $giftRegistry->getTitle() . '\' is present in grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry is absent in grid.';
    }
}

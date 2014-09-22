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
 * Class AssertGiftRegistryInGrid
 * Assert that gift registry is present in grid
 */
class AssertGiftRegistryInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created Gift Registry can be found at Gift Registry grid by title
     *
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistry $giftRegistry
     * @return void
     */
    public function processAssert(GiftRegistryIndex $giftRegistryIndex, GiftRegistry $giftRegistry)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $giftRegistryIndex->open()->getGiftRegistryGrid()->isGiftRegistryInGrid($giftRegistry),
            'Gift registry \'' . $giftRegistry->getTitle() . '\' is not present in grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry is present in grid.';
    }
}

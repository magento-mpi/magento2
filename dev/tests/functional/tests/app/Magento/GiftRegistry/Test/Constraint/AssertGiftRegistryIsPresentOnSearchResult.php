<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\GiftRegistry\Test\Page\GiftRegistrySearchResults;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;

/**
 * Class AssertGiftRegistryIsPresentOnSearchResult
 * Assert that created Gift Registry can be found in Search results grid
 */
class AssertGiftRegistryIsPresentOnSearchResult extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created Gift Registry can be found in Search results grid by title
     *
     * @param GiftRegistrySearchResults $giftRegistrySearchResults
     * @param GiftRegistry $giftRegistry
     * @return void
     */
    public function processAssert(GiftRegistrySearchResults $giftRegistrySearchResults, GiftRegistry $giftRegistry)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $giftRegistrySearchResults->getSearchResultsBlock()->isGiftRegistryInGrid($giftRegistry),
            'Gift registry \'' . $giftRegistry->getTitle() . '\' is not present in search results grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry is present in search results grid.';
    }
}

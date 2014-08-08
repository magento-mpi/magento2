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
use Magento\GiftRegistry\Test\Page\GiftRegistryView;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertGiftRegistryProductsIsPresent
 * Assert that product present in GiftRegistry on the gift registry search results page on the frontend
 */
class AssertGiftRegistryProductsIsPresent extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product visible in GiftRegistry on the gift registry search results by name
     *
     * @param GiftRegistrySearchResults $giftRegistrySearchResults
     * @param GiftRegistryView $giftRegistryView
     * @param GiftRegistry $giftRegistry
     * @param CatalogProductSimple $product
     * @return void
     */
    public function processAssert(
        GiftRegistrySearchResults $giftRegistrySearchResults,
        GiftRegistryView $giftRegistryView,
        GiftRegistry $giftRegistry,
        CatalogProductSimple $product
    ) {
        $giftRegistrySearchResults->getSearchResultsBlock()->giftRegistryView($giftRegistry);
        \PHPUnit_Framework_Assert::assertTrue(
            $giftRegistryView->getGiftRegistryItemsBlock()->isProductInGrid($product->getName()),
            'Product \'' . $product->getName()
            . '\' is not present in gift registry \'' . $giftRegistry->getTitle() . '\''
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product present in gift registry on the gift registry search results.';
    }
}

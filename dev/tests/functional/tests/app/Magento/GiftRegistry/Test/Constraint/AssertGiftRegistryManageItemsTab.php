<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;
use Magento\GiftRegistry\Test\Page\GiftRegistryItems;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertGiftRegistryManageItemsTab
 * Assert that Manage Items page on frontend contains correct product name and quantity
 */
class AssertGiftRegistryManageItemsTab extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Manage Items page on frontend contains correct product name and quantity
     *
     * @param CustomerAccountIndex $customerAccountIndex
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryItems $giftRegistryItems
     * @param GiftRegistry $giftRegistry
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(
        CustomerAccountIndex $customerAccountIndex,
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryItems $giftRegistryItems,
        GiftRegistry $giftRegistry,
        FixtureInterface $product
    ) {
        $customerAccountIndex->open()->getAccountMenuBlock()->openMenuItem("Gift Registry");
        $giftRegistryIndex->getGiftRegistryGrid()->eventAction($giftRegistry->getTitle(), 'Manage Items');
        $productName = $product->getName();
        $qty = $product->getCheckoutData()['qty'];
        \PHPUnit_Framework_Assert::assertTrue(
            $giftRegistryItems->getGiftRegistryItemsBlock()->isProductInGrid($product),
            'Product with name ' . $productName . ' and ' . $qty . ' quantity is absent in grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Manage Items page on frontend contains correct product name and quantity';
    }
}

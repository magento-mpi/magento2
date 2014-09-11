<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\TestCase;

use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Magento\GiftCardAccount\Test\Page\Adminhtml\NewIndex;

/**
 * Test Creation for DeleteGiftCardAccountEntity
 *
 * Test Flow:
 * 1. Login to the backend.
 * 2. Navigate to Marketing -> Gift Card Accounts.
 * 3. Select required gift card account from preconditions.
 * 4. Click on the "Delete" button.
 * 5. In confirmation popup message with text: "Are you sure you want to do this?" click "OK".
 * 6. Perform appropriate assertions.
 *
 * @group Gift_Card_Account_(CS)
 * @ZephyrId MAGETWO-24342
 */
class DeleteGiftCardAccountEntityTest extends Injectable
{
    /**
     * Page of gift card account
     *
     * @var Index
     */
    protected $giftCardAccountIndex;

    /**
     * Page of create gift card account
     *
     * @var NewIndex
     */
    protected $newIndex;

    /**
     * Create gift card account
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $product = $fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => '100_dollar_product']);
        $product->persist();
        $customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $customer->persist();
        return [
            'product' => $product,
            'customer' => $customer
        ];
    }

    /**
     * Inject gift card account page
     *
     * @param Index $index
     * @param NewIndex $newIndex
     * @return void
     */
    public function __inject(Index $index, NewIndex $newIndex)
    {
        $this->giftCardAccountIndex = $index;
        $this->newIndex = $newIndex;
    }

    /**
     * Delete gift card account entity
     *
     * @param GiftCardAccount $giftCardAccount
     * @return array
     */
    public function testDeleteGiftCardAccount(GiftCardAccount $giftCardAccount)
    {
        $giftCardAccount->persist();
        $this->giftCardAccountIndex->open();
        $code = $giftCardAccount->getCode();
        $this->giftCardAccountIndex->getGiftCardAccount()->searchAndOpen(['code' => $code]);
        $this->newIndex->getPageMainActions()->delete();
        return ['code' => $code];
    }
}

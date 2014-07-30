<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Magento\GiftCardAccount\Test\Page\Adminhtml\NewIndex;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;

/**
 * Test Creation for UpdateGiftCardAccountEntity
 *
 * Test Flow:
 * Precondition:
 * 1. Gift Card Account is created. Please, use source.
 *
 * Steps:
 * 1. Login to the backend.
 * 2. Navigate to Marketing -> Gift Card Accounts.
 * 3. Click on the gift card account from grid
 * 4. Edit test value(s) according to dataSet.
 * 5. Save Gift Card Account.
 * 6. Perform appropriate assertions.
 *
 * @group Gift_Card_(MX)
 * @ZephyrId MAGETWO-26665
 */
class UpdateGiftCardAccountEntityTest extends Injectable
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
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

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
        $this->fixtureFactory = $fixtureFactory;
        $customer->persist();
        return [
            'product' => $product,
            'customer' => $customer
        ];
    }

    /**
     * Inject gift card account pages
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
     * Update gift card account entity
     *
     * @param GiftCardAccount $giftCardAccountOrigin
     * @param GiftCardAccount $giftCardAccount
     * @return array
     */
    public function test(GiftCardAccount $giftCardAccountOrigin, GiftCardAccount $giftCardAccount)
    {
        $giftCardAccountOrigin->persist();
        $this->giftCardAccountIndex->open();
        $filter = ['code' => $giftCardAccount->getCode()];
        $this->giftCardAccountIndex->getGiftCardAccount()->searchAndOpen($filter);
        $this->newIndex->getPageMainForm()->fill($giftCardAccount);
        $this->newIndex->getPageMainActions()->save();
        return ['giftCardAccount' => $this->mergeFixture($giftCardAccount, $giftCardAccountOrigin)];
    }

    /**
     * Merge Gift Card Account fixture
     *
     * @param GiftCardAccount $giftCardAccount
     * @param GiftCardAccount $giftCardAccountOrigin
     * @return GiftCardAccount
     */
    protected function mergeFixture(GiftCardAccount $giftCardAccount, GiftCardAccount $giftCardAccountOrigin)
    {
        $data = array_merge($giftCardAccountOrigin->getData(), $giftCardAccount->getData());
        return $this->fixtureFactory->createByCode('giftCardAccount', ['data' => $data]);
    }
}

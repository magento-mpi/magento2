<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\TestCase;

use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Magento\GiftCardAccount\Test\Page\Adminhtml\NewIndex;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;

/**
 * Test Creation for CreateGiftCardAccountEntityTest
 *
 * Test Flow:
 * 1. Login to the backend.
 * 2. Navigate to Marketing -> Gift Card Accounts.
 * 3. Generate new code pool if it is needed (if appropriate error message is displayed on the page).
 * 4. Start to create Gift Card Account.
 * 5. Fill in data according to attached data set.
 * 6. Save Gift Card Account.
 * 7. Perform appropriate assertions.
 *
 * @group Gift_Card_(CS)
 * @ZephyrId MAGETWO-23865
 */
class CreateGiftCardAccountEntityTest extends Injectable
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
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => '100_dollar_product']
        );
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
     */
    public function __inject(Index $index, NewIndex $newIndex)
    {
        $this->giftCardAccountIndex = $index;
        $this->newIndex = $newIndex;
    }

    /**
     * Create gift card account entity
     *
     * @param GiftCardAccount $giftCardAccount
     * @return void
     */
    public function testCreateGiftCardAccount(GiftCardAccount $giftCardAccount)
    {
        // Steps
        $this->giftCardAccountIndex->open();
        $this->giftCardAccountIndex->getMessagesBlock()->clickLinkInMessages('error', 'here');
        $this->giftCardAccountIndex->getGridPageActions()->addNew();
        $this->newIndex->getPageMainForm()->fill($giftCardAccount);
        $this->newIndex->getPageMainActions()->save();
    }
}

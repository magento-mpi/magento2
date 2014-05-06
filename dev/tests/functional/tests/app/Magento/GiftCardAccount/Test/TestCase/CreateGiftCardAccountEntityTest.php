<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\TestCase;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
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
    private $index;

    /**
     * Page of create gift card account
     *
     * @var NewIndex
     */
    private $newIndex;

    /**
     * Customer fixture
     *
     * @var \Magento\Customer\Test\Fixture\CustomerInjectable
     */
    private $customerInjectable;

    /**
     * Storage main page
     *
     * @var CmsIndex
     */
    private $cmsIndex;

    /**
     * Customer login page
     *
     * @var CustomerAccountLogin
     */
    private $customerAccountLogin;

    /**
     * Check that user is login to frontend
     *
     * @var bool
     */
    private $isLogin = false;

    /**
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $catalogProductSimple = $fixtureFactory->
            createByCode('catalogProductSimple', ['dataSet' => '100_dollar_product']);
        $catalogProductSimple->persist();
        $this->customerInjectable = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $this->customerInjectable->persist();
        return ['catalogProductSimple' => $catalogProductSimple];
    }

    /**
     * Inject gift card account page
     *
     * @param Index $index
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param NewIndex $newIndex
     */
    public function __inject(
        Index $index,
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        NewIndex $newIndex
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->index = $index;
        $this->newIndex = $newIndex;
        $this->login();
    }

    /**
     * Create gift card account entity
     *
     * @param GiftCardAccount $giftCardAccount
     */
    public function testCreateGiftCardAccountEntity(GiftCardAccount $giftCardAccount)
    {
        // Steps
        $this->index->open();
        $this->index->getMessagesBlock()->clickLinkInMessages('error', 'here');
        $this->index->getGridPageActions()->addNew();
        $this->newIndex->getPageMainForm()->fill($giftCardAccount);
        $this->newIndex->getPageMainActions()->save();
    }

    /**
     * Login to frontend
     */
    public function login()
    {
        if ($this->isLogin) {
            return;
        }
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->isLogin = true;
        $this->customerAccountLogin->getLoginBlock()->login($this->customerInjectable);
    }
}

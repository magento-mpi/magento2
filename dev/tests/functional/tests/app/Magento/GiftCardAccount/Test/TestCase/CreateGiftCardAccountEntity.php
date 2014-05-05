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
use Magento\GiftCardAccount\Test\Page\Adminhtml\GiftCardAccountIndex;
use Magento\GiftCardAccount\Test\Page\Adminhtml\GiftCardAccountNewIndex;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;

/**
 * Test Creation for CreateGiftCardAccountEntity
 *
 * Test Flow:
 * 1.Login to the backend.
 * 2.Navigate to Marketing -> Gift Card Accounts.
 * 3.Generate new code pool if it is needed (if appropriate error message is displayed on the page).
 * 4.Start to create Gift Card Account.
 * 5.Fill in data according to attached data set.
 * 6.Save Gift Card Account.
 * 7.Perform appropriate assertions.
 *
 * @group Gift_Card_(CS)
 * @ZephyrId MAGETWO-23865
 */
class CreateGiftCardAccountEntity extends Injectable
{
    /** @var GiftCardAccountIndex $giftCardAccountIndex */
    protected $giftCardAccountIndex;

    /** @var GiftCardAccountNewIndex $giftCardAccountNewIndex */
    protected $giftCardAccountNewIndex;

    /** @var \Magento\Customer\Test\Fixture\CustomerInjectable $customer */
    protected $customer;

    /** @var CmsIndex $cmsIndex */
    protected $cmsIndex;

    /** @var CustomerAccountLogin $customerAccountLogin */
    protected $customerAccountLogin;

    /** @var  bool $isLogin */
    private $isLogin = false;

    /**
     * @param FixtureFactory $fixtureFactory
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $catalogProductSimple = $fixtureFactory->
            createByCode('catalogProductSimple', ['dataSet' => '100_dollar_product']);
        $catalogProductSimple->persist();
        $this->customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $this->customer->persist();
    }

    /**
     * Inject gift card account page
     *
     * @param GiftCardAccountIndex $giftCardAccountIndex
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param GiftCardAccountNewIndex $giftCardAccountNewIndex
     */
    public function __inject(
        GiftCardAccountIndex $giftCardAccountIndex,
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        GiftCardAccountNewIndex $giftCardAccountNewIndex
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->giftCardAccountIndex = $giftCardAccountIndex;
        $this->giftCardAccountNewIndex = $giftCardAccountNewIndex;
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
        $this->giftCardAccountIndex->open();
        $this->giftCardAccountIndex->getMessagesBlock()->clickLinkInMessages('error', 'here');
        $this->giftCardAccountIndex->getGridPageActions()->addNew();
        $this->giftCardAccountNewIndex->getPageMainForm()->fill($giftCardAccount);
        $this->giftCardAccountNewIndex->getPageMainActions()->save();
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
        $this->customerAccountLogin->getLoginBlock()->login($this->customer);
    }
}

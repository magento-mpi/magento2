<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;
use Magento\GiftRegistry\Test\Page\GiftRegistryAddSelect;
use Magento\GiftRegistry\Test\Page\GiftRegistryEdit;
use Magento\GiftRegistry\Test\Page\GiftRegistryShare;

/**
 * Test Creation for Sharing Frontend GiftRegistryEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Two Customers is  created on the frontend
 *
 * Steps:
 * 1. Login as registered customer1 to frontend
 * 2. Create Gift Registry
 * 3. Share Gift Registry with customer2
 * 4. Perform all assertions
 *
 * @group Gift_Registry_(CS)
 * @ZephyrId MAGETWO-27035
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShareGiftRegistryFrontendEntityTest extends Injectable
{
    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer account login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Customer account index page
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * Page CustomerAccountLogout
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Gift Registry index page
     *
     * @var GiftRegistryIndex
     */
    protected $giftRegistryIndex;

    /**
     * Gift Registry select type page
     *
     * @var GiftRegistryAddSelect
     */
    protected $giftRegistryAddSelect;

    /**
     * Gift Registry edit type page
     *
     * @var GiftRegistryEdit
     */
    protected $giftRegistryEdit;

    /**
     * Gift Registry share page
     *
     * @var GiftRegistryShare
     */
    protected $giftRegistryShare;

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CustomerAccountIndex $customerAccountIndex
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryAddSelect $giftRegistryAddSelect
     * @param GiftRegistryEdit $giftRegistryEdit
     * @param GiftRegistryShare $giftRegistryShare
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CustomerAccountIndex $customerAccountIndex,
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryAddSelect $giftRegistryAddSelect,
        GiftRegistryEdit $giftRegistryEdit,
        GiftRegistryShare $giftRegistryShare
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->giftRegistryIndex = $giftRegistryIndex;
        $this->giftRegistryAddSelect = $giftRegistryAddSelect;
        $this->giftRegistryEdit = $giftRegistryEdit;
        $this->giftRegistryShare = $giftRegistryShare;
    }

    /**
     * Create customers
     *
     * @param CustomerInjectable $customer1
     * @param CustomerInjectable $customer2
     * @return array
     */
    public function __prepare(
        CustomerInjectable $customer1,
        CustomerInjectable $customer2
    ) {
        $customer1->persist();
        $customer2->persist();

        return [
            'customer1' => $customer1,
            'customer2' => $customer2
        ];
    }

    /**
     * Sharing Frontend Gift Registry entity test
     *
     * @param CustomerInjectable $customer1
     * @param CustomerInjectable $customer2
     * @param GiftRegistry $giftRegistry
     * @param string $message
     * @return array
     */
    public function test(
        CustomerInjectable $customer1,
        CustomerInjectable $customer2,
        GiftRegistry $giftRegistry,
        $message
    ) {
        // Steps
        // Login as registered customer1 to frontend
        $this->customerAccountLogin->open()->getLoginBlock()->login($customer1);
        // Create Gift Registry
        $giftRegistry->persist();
        // Share Gift Registry with customer2
        $this->giftRegistryIndex->getGiftRegistryGrid()->eventAction($giftRegistry->getTitle(), 'Share');
        $recipients = [$customer2];
        $this->giftRegistryShare->getGiftRegistryShareForm()->fillForm($message, $recipients);
        $this->giftRegistryShare->getGiftRegistryShareForm()->shareGiftRegistry();

        return [
            'recipients' => $recipients
        ];
    }

    /**
     * Log out after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}

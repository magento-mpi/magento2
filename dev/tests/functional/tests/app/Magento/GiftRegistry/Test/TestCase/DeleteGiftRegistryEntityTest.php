<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Mtf\TestCase\Injectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;

/**
 * Test Creation for Delete frontend GiftRegistryEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Register Customer
 * 2. Gift Registry created
 *
 * Steps:
 * 1. Go to frontend
 * 2. Login as a Customer
 * 3. Go to My Account -> Gift Registry
 * 4. Press on appropriate Gift Registry "Delete" button
 * 5. Perform Asserts
 *
 * @group Gift_Registry_(CS)
 * @ZephyrId MAGETWO-26648
 */
class DeleteGiftRegistryEntityTest extends Injectable
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
     * Gift Registry index page
     *
     * @var GiftRegistryIndex
     */
    protected $giftRegistryIndex;

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param GiftRegistryIndex $giftRegistryIndex
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        GiftRegistryIndex $giftRegistryIndex
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->giftRegistryIndex = $giftRegistryIndex;
    }

    /**
     * Create customer and product
     *
     * @param CatalogProductSimple $product
     * @param CustomerInjectable $customer
     * @return array
     */
    public function __prepare(
        CatalogProductSimple $product,
        CustomerInjectable $customer
    ) {
        $product->persist();
        $customer->persist();

        return [
            'customer' => $customer,
            'product' => $product
        ];
    }

    /**
     * Create Gift Registry entity test
     *
     * @param CustomerInjectable $customer
     * @param GiftRegistry $giftRegistry
     * @return void
     */
    public function test(CustomerInjectable $customer, GiftRegistry $giftRegistry)
    {
        // Steps
        if (!$this->cmsIndex->open()->getLinksBlock()->isLinkVisible('Log Out')) {
            $this->cmsIndex->open()->getLinksBlock()->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($customer);
        }
        $giftRegistry->persist();
        $this->cmsIndex->open()->getLinksBlock()->openLink("My Account");
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem('Gift Registry');
        $this->giftRegistryIndex->getGiftRegistryGrid()->eventAction($giftRegistry->getTitle(), 'Delete');
    }
}

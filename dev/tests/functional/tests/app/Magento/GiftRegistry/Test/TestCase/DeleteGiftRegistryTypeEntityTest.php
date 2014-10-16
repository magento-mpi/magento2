<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\TestCase;

use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\GiftRegistry\Test\Fixture\GiftRegistryType;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryIndex;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryNew;

/**
 * Test Creation for DeleteGiftRegistryTypeEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create gift registry type
 *
 * Steps:
 * 1. Log in to Backend
 * 2. Navigate to Stores > Gift Registry
 * 3. Open created gift registry
 * 4. Click Delete
 * 5. Perform all assertions
 *
 * @group Gift_Registry_(CS)
 * @ZephyrId MAGETWO-27352
 */
class DeleteGiftRegistryTypeEntityTest extends Injectable
{
    /**
     * GiftRegistryIndex page
     *
     * @var GiftRegistryIndex
     */
    protected $giftRegistryIndex;

    /**
     * GiftRegistryNew page
     *
     * @var GiftRegistryNew
     */
    protected $giftRegistryNew;

    /**
     * CmsIndex page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * CustomerAccountLogout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Preparing customer for constraints
     *
     * @param CustomerInjectable $customer
     * @return array
     */
    public function __prepare(CustomerInjectable $customer)
    {
        $customer->persist();
        return ['customer' => $customer];
    }

    /**
     * Preparing pages for test
     *
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryNew $giftRegistryNew
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __inject(
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryNew $giftRegistryNew,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->giftRegistryIndex = $giftRegistryIndex;
        $this->giftRegistryNew = $giftRegistryNew;
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Run Test Creation for DeleteGiftRegistryTypeEntity
     *
     * @param GiftRegistryType $giftRegistryType
     * @return void
     */
    public function test(GiftRegistryType $giftRegistryType)
    {
        $this->markTestIncomplete('Bug: MAGETWO-28824');

        // Preconditions:
        $giftRegistryType->persist();

        // Steps:
        $filter = ['label' => $giftRegistryType->getLabel()];
        $this->giftRegistryIndex->open();
        $this->giftRegistryIndex->getGiftRegistryGrid()->searchAndOpen($filter);
        $this->giftRegistryNew->getPageActions()->delete();
    }

    /**
     * Tear down after variation
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}

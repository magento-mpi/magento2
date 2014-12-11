<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\TestCase;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\GiftRegistry\Test\Fixture\GiftRegistryType;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryIndex;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryNew;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for UpdateGiftRegistryTypeEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create gift registry type
 *
 * Steps:
 * 1. Log in to Backend
 * 2. Navigate to Stores > Gift Registry
 * 3. Open created gift registry
 * 4. Update data according to dataSet
 * 5. Perform all assertions
 *
 * @group Gift_Registry_(CS)
 * @ZephyrId MAGETWO-27333
 */
class UpdateGiftRegistryTypeEntityTest extends Injectable
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
     * Run Test Creation for UpdateGiftRegistryTypeEntity
     *
     * @param GiftRegistryType $giftRegistryType
     * @param GiftRegistryType $giftRegistryTypeInitial
     * @return void
     */
    public function test(GiftRegistryType $giftRegistryType, GiftRegistryType $giftRegistryTypeInitial)
    {
        // Steps
        $giftRegistryTypeInitial->persist();
        $filter = ['label' => $giftRegistryTypeInitial->getLabel()];
        $this->giftRegistryIndex->open();
        $this->giftRegistryIndex->getGiftRegistryGrid()->searchAndOpen($filter);
        $this->giftRegistryNew->getGiftRegistryForm()->fill($giftRegistryType);
        $this->giftRegistryNew->getPageActions()->save();
    }
}

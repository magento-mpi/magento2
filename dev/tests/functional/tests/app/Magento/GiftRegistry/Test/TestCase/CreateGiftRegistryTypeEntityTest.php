<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\TestCase;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\GiftRegistry\Test\Fixture\GiftRegistryType;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryIndex;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryNew;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for CreateGiftRegistryTypeEntity
 *
 * Test Flow:
 * Steps:
 * 1. Log in to Backend
 * 2. Navigate to Stores > Gift Registry
 * 3. Click "Add Gift Registry Type"
 * 4. Fill data according to dataSet
 * 5. Save gift registry
 * 6. Perform all assertions
 *
 * @group Gift_Registry_(CS)
 * @ZephyrId MAGETWO-27146
 */
class CreateGiftRegistryTypeEntityTest extends Injectable
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
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function __inject(GiftRegistryIndex $giftRegistryIndex, GiftRegistryNew $giftRegistryNew, CmsIndex $cmsIndex)
    {
        $this->giftRegistryIndex = $giftRegistryIndex;
        $this->giftRegistryNew = $giftRegistryNew;
        $this->cmsIndex = $cmsIndex;
    }

    /**
     * Run CreateGiftRegistryTypeEntityTest
     *
     * @param GiftRegistryType $giftRegistryType
     * @return void
     */
    public function test(GiftRegistryType $giftRegistryType)
    {
        $this->giftRegistryIndex->open();
        $this->giftRegistryIndex->getPageActions()->addNew();
        $this->giftRegistryNew->getGiftRegistryForm()->fill($giftRegistryType);
        $this->giftRegistryNew->getPageActions()->save();
    }

    /**
     * Tear down after variation
     *
     * @return void
     */
    public function tearDown()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink('Log Out');
    }
}

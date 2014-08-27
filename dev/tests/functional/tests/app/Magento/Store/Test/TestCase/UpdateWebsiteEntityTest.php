<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Store\Test\Fixture\Website;
use Magento\Backend\Test\Page\Adminhtml\StoreIndex;
use Magento\Backend\Test\Page\Adminhtml\EditWebsite;

/**
 * Update Website (Store Management)
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create website
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Stores-> All Stores
 * 3. Open created website
 * 4. Fill data according to dataset
 * 5. Click "Save Web Site" button
 * 6. Perform all assertions
 *
 * @group Store_Management_(PS)
 * @ZephyrId MAGETWO-27690
 */
class UpdateWebsiteEntityTest extends Injectable
{
    /**
     * Page StoreIndex
     *
     * @var StoreIndex
     */
    protected $storeIndex;

    /**
     * Page EditWebsite
     *
     * @var EditWebsite
     */
    protected $editWebsite;

    /**
     * Injection data
     *
     * @param StoreIndex $storeIndex
     * @param EditWebsite $editWebsite
     * @return void
     */
    public function __inject(
        StoreIndex $storeIndex,
        EditWebsite $editWebsite
    ) {
        $this->storeIndex = $storeIndex;
        $this->editWebsite = $editWebsite;
    }

    /**
     * Update Website
     *
     * @param Website $websiteOrigin
     * @param Website $website
     * @return void
     */
    public function test(Website $websiteOrigin, Website $website)
    {
        //Preconditions
        $websiteOrigin->persist();

        //Steps
        $this->storeIndex->open();
        $this->storeIndex->getStoreGrid()->searchAndOpenWebsite($websiteOrigin);
        $this->editWebsite->getEditFormWebsite()->fill($website);
        $this->editWebsite->getFormPageActions()->save();
    }
}

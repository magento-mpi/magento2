<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Store\Test\Fixture\StoreGroup;
use Magento\Backend\Test\Page\Adminhtml\StoreIndex;
use Magento\Backend\Test\Page\Adminhtml\EditGroup;

/**
 * Update StoreGroup (Store Management)
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create store
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Stores-> All Stores
 * 3. Open created store
 * 4. Fill data according to dataset
 * 5. Click "Save Store" button
 * 6. Perform all assertions
 *
 * @group Store_Management_(PS)
 * @ZephyrId MAGETWO-27568
 */
class UpdateStoreGroupEntityTest extends Injectable
{
    /**
     * Page StoreIndex
     *
     * @var StoreIndex
     */
    protected $storeIndex;

    /**
     * Page EditGroup
     *
     * @var EditGroup
     */
    protected $editGroup;

    /**
     * Injection data
     *
     * @param StoreIndex $storeIndex
     * @param EditGroup $editGroup
     * @return void
     */
    public function __inject(
        StoreIndex $storeIndex,
        EditGroup $editGroup
    ) {
        $this->storeIndex = $storeIndex;
        $this->editGroup = $editGroup;
    }

    /**
     * Update New StoreGroup
     *
     * @param StoreGroup $storeGroupOrigin
     * @param StoreGroup $storeGroup
     * @return void
     */
    public function test(StoreGroup $storeGroupOrigin, StoreGroup $storeGroup)
    {
        //Preconditions
        $storeGroupOrigin->persist();

        //Steps
        $this->storeIndex->open();
        $this->storeIndex->getStoreGrid()->searchAndOpenStore($storeGroupOrigin);
        $this->editGroup->getEditFormGroup()->fill($storeGroup);
        $this->editGroup->getFormPageActions()->save();
    }
}

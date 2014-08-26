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
use Magento\Backend\Test\Page\Adminhtml\NewGroupIndex;

/**
 * Create New StoreGroup (Store Management)
 *
 * Test Flow:
 * 1. Open Backend
 * 2. Go to Stores-> All Stores
 * 3. Click "Create Store" button
 * 4. Fill data according to dataset
 * 5. Click "Save Store" button
 * 6. Perform all assertions
 *
 * @group Store_Management_(PS)
 * @ZephyrId MAGETWO-27345
 */
class CreateStoreGroupEntityTest extends Injectable
{
    /**
     * Page StoreIndex
     *
     * @var StoreIndex
     */
    protected $storeIndex;

    /**
     * NewGroupIndex page
     *
     * @var NewGroupIndex
     */
    protected $newGroupIndex;

    /**
     * Injection data
     *
     * @param StoreIndex $storeIndex
     * @param NewGroupIndex $newGroupIndex
     * @return void
     */
    public function __inject(
        StoreIndex $storeIndex,
        NewGroupIndex $newGroupIndex
    ) {
        $this->storeIndex = $storeIndex;
        $this->newGroupIndex = $newGroupIndex;
    }

    /**
     * Create New StoreGroup
     *
     * @param StoreGroup $storeGroup
     * @return void
     */
    public function test(StoreGroup $storeGroup)
    {
        //Steps
        $this->storeIndex->open();
        $this->storeIndex->getGridPageActions()->createStoreGroup();
        $this->newGroupIndex->getEditFormGroup()->fill($storeGroup);
        $this->newGroupIndex->getFormPageActions()->save();
    }
}

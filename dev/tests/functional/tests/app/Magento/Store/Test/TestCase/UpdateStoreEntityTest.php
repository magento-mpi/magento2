<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\TestCase;

use Magento\Store\Test\Fixture\Store;
use Magento\Backend\Test\Page\Adminhtml\StoreIndex;
use Magento\Backend\Test\Page\Adminhtml\StoreNew;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for UpdateStoreEntity (Store Management)
 *
 * Test Flow:
 * Preconditions:
 * 1.Create store view
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Stores -> All Stores
 * 3. Open created store view
 * 4. Fill data according to dataset
 * 5. Perform all assertions
 *
 * @group Store_Management_(PS)
 * @ZephyrId MAGETWO-27786
 */
class UpdateStoreEntityTest extends Injectable
{
    /**
     * Page StoreIndex
     *
     * @var StoreIndex
     */
    protected $storeIndex;

    /**
     * Page StoreNew
     *
     * @var StoreNew
     */
    protected $storeNew;

    /**
     * Preparing pages for test
     *
     * @param StoreIndex $storeIndex
     * @param StoreNew $storeNew
     * @return void
     */
    public function __inject(StoreIndex $storeIndex, StoreNew $storeNew)
    {
        $this->storeIndex = $storeIndex;
        $this->storeNew = $storeNew;
    }

    public function test(FixtureFactory $fixtureFactory, Store $store)
    {
        // Preconditions:
        $storeInitial = $fixtureFactory->createByCode('store');
        $storeInitial->persist();

        // Steps:
        $this->storeIndex->open();
        $this->storeIndex->getStoreGrid()->editStore($storeInitial->getName());
        $this->storeNew->getStoreForm()->fill($store);
        $this->storeNew->getFormPageActions()->save();
    }
}

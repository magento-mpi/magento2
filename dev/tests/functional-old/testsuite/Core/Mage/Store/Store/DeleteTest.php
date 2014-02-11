<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Delete Store in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Store_Store_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Stores</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
    }

    /**
     * <p>Delete Store without Store View</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3489
     */
    public function deleteWithoutStoreView()
    {
        //Preconditions
        $storeData = $this->loadDataSet('Store', 'generic_store');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        //Data
        $deleteStoreData = array('store_name' => $storeData['store_name']);
        //Steps
        $this->storeHelper()->deleteStore($deleteStoreData);
    }

    /**
     * <p>Delete Store with Store View</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3488
     */
    public function deletableWithStoreView()
    {
        //Preconditions
        $storeData = $this->loadDataSet('Store', 'generic_store');
        $storeViewData =
            $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Data
        $deleteStoreData = array('store_name' => $storeData['store_name']);
        //Steps
        $this->storeHelper()->deleteStore($deleteStoreData);
    }
}
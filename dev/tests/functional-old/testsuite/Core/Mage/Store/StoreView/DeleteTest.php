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
 * Delete Store View in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Store_StoreView_DeleteTest extends Mage_Selenium_TestCase
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
     * <p>Create Store View. Fill in only required fields.</p>
     *
     * @return string
     * @test
     * @TestlinkId TL-MAGE-3486
     */
    public function creationStoreView()
    {
        //Data
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
        //Steps
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_store_view');

        return $storeViewData['store_view_name'];
    }

    /**
     * <p>Delete Store View Without creating DB backup</p>
     *
     * @param string $storeView
     *
     * @test
     * @depends creationStoreView
     * @TestlinkId TL-MAGE-3487
     */
    public function deleteStoreViewWithoutBackup($storeView)
    {
        //Data
        $storeData = array('store_view_name' => $storeView);
        //Steps
        $this->storeHelper()->deleteStore($storeData);
    }
}
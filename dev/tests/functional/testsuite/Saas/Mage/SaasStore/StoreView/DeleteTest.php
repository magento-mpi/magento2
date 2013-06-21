<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Mage_SaasStore_StoreView_DeleteTest extends Saas_Mage_TestCase
{
    /**
     * @var array
     */
    protected $_storeViewData;

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Stores</p>
     * <p>Create Store View</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();

        $this->_storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
        $this->saasStoreHelper()->createStoreView($this->_storeViewData);
    }

    /**
     * <p>Delete Store View Without creating DB backup</p>
     *
     * @test
     * @group goinc
     */
    public function deleteStoreViewWithoutBackup()
    {
        $this->navigate('manage_stores');

        $this->saasStoreHelper()->deleteStoreView(array('store_view_name' => $this->_storeViewData['store_view_name']));
    }
}

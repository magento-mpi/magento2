<?php
/**
 * {license_notice}
 *
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @method string processString processString(string $string)
 * @method array parsePath parsePath(string $path)
 */
class Saas_Mage_SaasStore_Helper extends Enterprise_Mage_Product_Helper
{
    /**
     * Open Manage Stores
     *
     * Pre condition: An user logged in dashboard. Dashboard is opened.
     *
     * @return Saas_Mage_SaasStore_Helper
     */
    public function openManageStores()
    {
        $this->navigate('manage_stores');

        return $this;
    }

    /**
     * Create Store
     *
     * Pre condition: An user logged in dashboard. Dashboard is opened.
     *
     * @param array $data
     * @return Saas_Mage_SaasStore_Helper
     */
    public function createStoreView(array $data)
    {
        $this->openManageStores();
        $this->storeHelper()->createStore($data, 'store_view');

        $this->assertMessagePresent('success', 'success_saved_store_view');

        return $this;
    }

    /**
     * Delete Store
     *
     * Pre condition: An user logged in dashboard. Dashboard is opened.
     *
     * @param array $data
     * @return Saas_Mage_SaasStore_Helper
     */
    public function deleteStoreView(array $data)
    {
        $this->openManageStores()
            ->searchStoreView($data)
            ->openStoreView($data['store_view_name'])
            ->clickButton('delete_store_view', false);

        $msg = 'Deleting a Store View will not delete the information associated with the Store View (e.g. categories,'
            . ' products, etc.), but the Store View will not be able to be restored. Are you sure you want to do this?';
        $this->assertSame($msg, $this->alertText(), 'actual and expected confirmation message does not match');
        $this->acceptAlert();

        $this->waitForPageToLoad();
        $this->validatePage('manage_stores');
        $this->assertMessagePresent('success', 'success_deleted_store_view');

        return $this;
    }

    /**
     * Search Store View
     *
     * Pre condition: Manage stores page is opened.
     *
     * @param array $data
     * @return Saas_Mage_SaasStore_Helper
     */
    public function searchStoreView(array $data)
    {
        $this->clickButton('reset_filter');
        $this->fillFieldset($data, 'manage_stores');
        $this->clickButton('search');

        return $this;
    }

    /**
     * Open Store View
     *
     * Pre condition: Manage stores page is opened.
     *
     * @param string $title
     * @return Saas_Mage_SaasStore_Helper
     */
    public function openStoreView($title)
    {
        $this->addParameter('elementTitle', $title);
        $this->clickControl('link', 'select_store_view');

        return $this;
    }
}

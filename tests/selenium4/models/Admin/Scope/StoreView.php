<?php
/**
 * Admin_Scope_StoreView model
 *
 * @author Magento Inc.
 */
class Model_Admin_Scope_StoreView extends Model_Admin {
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->storeViewData = Core::getEnvConfig('backend/scope/store_view');
    }

    /**
     * Create storeView sequence test
     *
     * @param array $params May contain the following params:
     * name, storeName, code, order, status
     */
    public function doCreate($params = array())
    {
        $this->printDebug('doCreateStoreView started...');

        $storeViewData = $params ? $params : $this->storeViewData;

        $storeName = Core::getEnvConfig('backend/scope/store/name');
        $name = $storeViewData['name'];
        $code = $storeViewData['code'];
        $sort_order = $storeViewData['order'];
        $status = $storeViewData['status'];

        $this->printDebug('$name=' . $name);
        $this->printDebug('$code=' . $code);

        $this->clickAndWait(
            $this->getUiElement("/admin/topmenu/system/managestores/link/openpage")
        );

        $this->clickAndWait($this->getUiElement("/admin/pages/system/scope/manage_stores/buttons/create_web_store_view"));

        //Fill all fields
        $this->setUiNamespace('admin/pages/system/scope/store_view');
        $this->select($this->getUiElement("selectors/store"), $storeName);
        $this->type($this->getUiElement("inputs/name"), $name);
        $this->type($this->getUiElement("inputs/code"), $code);
        $this->select($this->getUiElement("selectors/status"), $status);
        $this->type($this->getUiElement("inputs/sort_order"), $sort_order);
        $this->clickAndWait($this->getUiElement("buttons/save"));

        // check for error message
        if ($this->waitForElement($this->getUiElement('/admin/messages/error'),1)) {
            $etext = $this->getText($this->getUiElement('/admin/messages/error'));
            $this->setVerificationErrors('doStoreViewCreate: ' . $etext);
        } else {
          // Check for success message
          if ($this->waitForElement($this->getUiElement('/admin/messages/success'),1)) {
            $this->printInfo('Store View ' . $storeViewData['name'] . ' has been created');
          } else {
            $this->setVerificationErrors('doStoreViewCreate: no success message');
          }
        }
        $this->printDebug('doStoreViewCreate finished');

    }

    /**
     * Open store view from admin. If there are several stores with same stor view ename - will open first one
     * @param name, code
     * @return boolean
     */
    public function doOpen($params = array())
    {
        $this->printDebug('doOpenStoreView started...');
        $storeViewData = $params ? $params : $this->storeViewData;
        $name = $storeViewData['name'];
        $code = $storeViewData['code'];
        $order = $storeViewData['order'];
        $status = $storeViewData['status'];

        $this->printDebug('$name=' . $name);
        $this->printDebug('$code=' . $code);

        $this->setUiNamespace('/admin/pages/system/scope/manage_stores');
        //Open ManageStores
        $this->clickAndWait(
            $this->getUiElement("/admin/topmenu/system/managestores/link/openpage")
        );

        // Filter users by store view name
        $this->click($this->getUiElement('buttons/reset_filter'));
        sleep(1);
        //Filter by store view name
        $this->waitForElement($this->getUiElement('filters/store_view_name'),10);
        $this->type($this->getUiElement('filters/store_view_name'),$name);
        $this->clickAndWait($this->getUiElement('buttons/search'));
        sleep(1);
        //Open user with 'Store Name View' == name
        if ($this->isTextPresent($this->getUiElement('/admin/global/elements/no_records'))) {
          // Store View not founded
          $this->printDebug('doOpenStoreView finished with false');
          return false;
        } else {
            //Open Store
            $this->clickAndWait($this->getUiElement('elements/body') . '//tr[1]/td[3]//a');
            $this->printInfo('Store View ' . $name . ' opened');
            return true;
        }
    }

    /**
     * Delete store view
     *
     * @param array $params should contain the following params:
     * name
     */
    public function doDelete($params = array())
    {
        $this->printDebug('doStoreViewDelete started...');
        $storeViewData = $params ? $params : $this->storeViewData;
        $name = $storeViewData['name'];

        $this->clickAndWait(
            $this->getUiElement("/admin/topmenu/system/managestores/link/openpage")
        );

        if ($this->doOpen($storeViewData)) {
            $this->setUiNamespace('/admin/pages/system/scope/store_view');
            //Delete site
            if ($this->waitForElement($this->getUiElement('buttons/delete'),2))  {
                //Delete Store View
                $this->clickAndWait($this->getUiElement('buttons/delete'));
                //Select No backup
                $this->waitForElement($this->getUiElement('/admin/pages/system/scope/create_backup/selectors/create_backup'),5);
                $this->select($this->getUiElement('/admin/pages/system/scope/create_backup/selectors/create_backup'),'label=No');
                //Delete Store
                $this->click($this->getUiElement('buttons/delete'));
                $this->waitForElement($this->getUiElement('/admin/pages/system/scope/manage_stores/elements/store_table'),130);

                // check for error message
                if ($this->waitForElement($this->getUiElement('/admin/messages/error'),1)) {
                    $etext = $this->getText($this->getUiElement('/admin/messages/error'));
                    $this->setVerificationErrors('doStoreViewDelete: ' . $etext);
                } else {
                  // Check for success message
                  if ($this->waitForElement($this->getUiElement('/admin/messages/success'),1)) {
                    $this->printInfo('Store View ' . $name . ' has been deleted');
                  } else {
                    $this->setVerificationErrors('doStoreViewDelete: no success message');
                  }
                }
            } else {
                $this->printInfo('Store View ' . $name . ' could not be deleted');
            }
        }
        $this->printDebug('doStoreViewDelete finished');
    }

}

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

        $a = Core::getEnvConfig('backend/scope/storeview');
        $a['storeName'] = Core::getEnvConfig('backend/scope/store/storename');
        $this->storeViewData = $a;
    }

    /**
     * Create storeView sequence test
     *
     * @param array $params May contain the following params:
     * name, storeName, code, order, status
     */
    public function doCreate($params = array())
    {
        $storeViewData = $params ? $params : $this->storeViewData;

        $this->clickAndWait(
            $this->getUiElement("admin/topmenu/system/managestores/link/openpage")
        );

        $this->setUiNamespace('admin/pages/system/managestores/createsite');

        $this->clickAndWait($this->getUiElement("buttons/createstoreview"));
        $this->select($this->getUiElement("select/store"), $storeViewData['storeName']);
        $this->type($this->getUiElement("inputs/storeviewname"), $storeViewData['name']);
        $this->type($this->getUiElement("inputs/storeviewcode"), $storeViewData['code']);
        $this->select($this->getUiElement("select/storestatus"), $storeViewData['status']);
        $this->clickAndWait($this->getUiElement("buttons/savestoreview"));

        //check for successful message
        if (!$this->isElementPresent($this->getUiElement("messages/saved"))) {
            $this->setVerificationErrors("Check 1:  No successfull message");
        }
    }
}

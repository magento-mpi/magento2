<?php
/**
 * Admin_Scope_Store model
 *
 * @author Magento Inc.
 */
class Model_Admin_Scope_Store extends Model_Admin {
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->storeData = array(
            'name'         => Core::getEnvConfig('backend/scope/store/storename'),
            'siteName'     => Core::getEnvConfig('backend/scope/site/name'),
            'rootCategory' => Core::getEnvConfig('backend/managecategories/rootname'),
        );
    }

    /**
     * Create store sequence test
     *
     * @param array $params May contain the following params:
     * name, siteName, rootCategory
     */
    public function doCreate($params = array())
    {
        $storeData = $params ? $params : $this->storeData;

        $this->clickAndWait(
            $this->getUiElement("/admin/topmenu/system/managestores/link/openpage")
        );

        $this->setUiNamespace('admin/pages/system/managestores/createsite');

        $this->clickAndWait($this->getUiElement("buttons/createwebstore"));
        $this->select($this->getUiElement("select/site"), $storeData['siteName']);
        $this->type($this->getUiElement("inputs/storename"), $storeData['name']);
        $this->select($this->getUiElement("select/rootcategory"), $storeData['rootCategory']);
        $this->clickAndWait($this->getUiElement("buttons/savestore"));

        // Check for successful message
        if (!$this->isElementPresent($this->getUiElement("messages/saved"))) {
            $this->setVerificationErrors("Check 1:  No successfull message");
        }
    }
}

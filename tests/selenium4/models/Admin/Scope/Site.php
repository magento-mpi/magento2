<?php
/**
 * Admin_Scope_Site model
 *
 * @author Magento Inc.
 */
class Model_Admin_Scope_Site extends Model_Admin {
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->siteData = Core::getEnvConfig('backend/scope/site');
    }

    /**
     * Create site sequence test
     *
     * @param array $params May contain the following params:
     * name, code, sortorder
     */
    public function doCreate($params = array())
    {
        $siteData = $params ? $params : $this->siteData;

        $this->clickAndWait(
            $this->getUiElement("/admin/topmenu/system/managestores/link/openpage")
        );

        $this->setUiNamespace('admin/pages/system/managestores/createsite');

        $this->clickAndWait($this->getUiElement("buttons/createwebsite"));
        $this->type($this->getUiElement("inputs/name"), $siteData['name']);
        $this->type($this->getUiElement("inputs/code"), $siteData['code']);
        $this->type($this->getUiElement("inputs/order"), $siteData['sortorder']);
        $this->clickAndWait($this->getUiElement("buttons/save"));

        // Check for successful message
        if (!$this->isElementPresent($this->getUiElement("messages/saved"))) {
            $this->setVerificationErrors("Check 1: No successfull message");
        }
    }
}

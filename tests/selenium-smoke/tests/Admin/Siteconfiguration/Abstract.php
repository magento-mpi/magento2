<?php
/**
 * Abstract test class for Admin module
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_Siteconfiguration_Abstract extends Test_Admin_Abstract
{
    /**
     * Helper local instance
     *
     * @var Helper_Admin
     */
    protected $_helper = null;

    /**
     * Initialize the environment
     */
    public function  setUp() {
        parent::setUp();

        // Get test parameters
        $this->_siteName = Core::getEnvConfig('backend/managestores/site/name');
        $this->_siteCode = Core::getEnvConfig('backend/managestores/site/code');
    }

    /**
     * Admin-System-Configuration-Web: configurate base URL
     *
     */
     public function configURL() {
        Core::debug("adding URL started");
        $this->clickAndWait($this->getUiElement("admin/topmenu/system/configuration"));
        $this->selectAndWait($this->getUiElement("admin/pages/system/configuration/selectors/store"), $this->_siteName);
        $this->clickAndWait($this->getUiElement("admin/pages/system/configuration/buttons/web"));
        $this->click($this->getUiElement("admin/pages/system/configuration/buttons/secure"));
        if (!$this->isElementPresent("//fieldset[@id='web_secure' and @style='display: none']")) {
            $this->click($this->getUiElement("admin/pages/system/configuration/checkboxes/secure/baselink"));
        }
        $this->type($this->getUiElement("admin/pages/system/configuration/inputs/secure/baselink"), '{{secure_base_url}}' . $this->_siteCode . '/');
        $this->click($this->getUiElement("admin/pages/system/configuration/buttons/unsecure"));
        if (!$this->isElementPresent("//fieldset[@id='web_unsecure' and @style='display: none']")) {
            $this->click($this->getUiElement("admin/pages/system/configuration/checkboxes/unsecure/baselink"));
        }
        $this->type($this->getUiElement("admin/pages/system/configuration/inputs/unsecure/baselink"), '{{unsecure_base_url}}' . $this->_siteCode . '/');
        $this->click($this->getUiElement("admin/pages/system/configuration/buttons/save"));
        $this->waitForElement($this->getUiElement("admin/messages/success"), 30);

        //check for successful message
        if (!$this->isElementPresent($this->getUiElement("admin/messages/success"))) {
            $this->setVerificationErrors("Check 1 : No successfull message");
        }
        if (!$this->isTextPresent($this->getUiElement("admin/pages/system/configuration/inputs/secure/baselink"), '{{secure_base_url}}' . $this->_siteCode . '/')) {
            $this->setVerificationErrors("Check 2 : value wasn't saved");
        }
        if (!$this->isTextPresent($this->getUiElement("admin/pages/system/configuration/inputs/unsecure/baselink"), '{{unsecure_base_url}}' . $this->_siteCode . '/')) {
            $this->setVerificationErrors("Check 3 : value wasn't saved");
        }
        Core::debug("adding URL finished");       
     }
    /**
     * Admin-System-Configuration: Reindex Data
     *
     */
     public function reindex() {
        Core::debug("reindex...");
        $this->clickAndWait($this->getUiElement("admin/topmenu/system/indexmanagement"));
        $this->click($this->getUiElement("admin/pages/system/reindex/buttons/selectall"));
        $this->click($this->getUiElement("admin/pages/system/reindex/buttons/submit"));
        $this->waitForElement($this->getUiElement("admin/messages/success"), 60);

        //check for successful message
        if (!$this->isElementPresent($this->getUiElement("admin/messages/success"))) {
            $this->setVerificationErrors("Check 1 : No successfull message");
        }
        if (!$this->isTextPresent($this->getUiElement("admin/pages/system/reindex/messages/allsuccess"))) {
            $this->setVerificationErrors("Check 2 : data wasn't reindexed");
        }
        Core::debug("reidex finished");
     }

}


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
    }

    /**
     * Admin-System-Configuration-Web: configurate base URL
     *
     */
     public function configURL() {
        Core::debug("adding URL started",7);

        //Open SystemConfiguration
        $this->clickAndWait($this->getUiElement("admin/topmenu/system/configuration"));

        //Select site
        $this->selectAndWait($this->getUiElement("admin/pages/system/configuration/selectors/store"), $this->_siteName);

        //Open web tab
        $this->clickAndWait($this->getUiElement("admin/pages/system/configuration/links/web"));

        //Open secure section
        if ($this->waitForElement($this->getUiElement("admin/pages/system/configuration/tabs/web/elements/hidedSecure"),1)) {
            $this->click($this->getUiElement("admin/pages/system/configuration/tabs/web/links/secure"));
        };
        //uncheck 'Usedefault' if neccessary
        if ($this->isElementPresent($this->getUiElement('admin/pages/system/configuration/tabs/web/checkboxes/secure/useDefaultBaseLinkChecked'))) {
            $this->click($this->getUiElement('admin/pages/system/configuration/tabs/web/checkboxes/secure/useDefaultBaseLink'));
        }       
        //Add prefix to base-url
        $this->type($this->getUiElement("admin/pages/system/configuration/tabs/web/inputs/secure/baselink"), '{{secure_base_url}}' . $this->_siteCode . '/');

        //Open unSecure section
        if ($this->waitForElement($this->getUiElement("admin/pages/system/configuration/tabs/web/elements/hidedUnsecure"),1)) {
            $this->click($this->getUiElement("admin/pages/system/configuration/tabs/web/links/unsecure"));
        };
        //uncheck 'Usedefault' if neccessary
        if ($this->isElementPresent($this->getUiElement('admin/pages/system/configuration/tabs/web/checkboxes/unsecure/useDefaultBaseLinkChecked'))) {
            $this->click($this->getUiElement('admin/pages/system/configuration/tabs/web/checkboxes/unsecure/useDefaultBaseLink'));
        }
        //Add prefix to base-url
        $this->type($this->getUiElement("admin/pages/system/configuration/tabs/web/inputs/unsecure/baselink"), '{{unsecure_base_url}}' . $this->_siteCode . '/');

        //Save Configuration
        $this->click($this->getUiElement("admin/pages/system/configuration/buttons/save"));
        

        // check for error message
        if ($this->waitForElement($this->getUiElement("admin/messages/error"),1)) {
            $etext = $this->getText($this->getUiElement("admin/messages/error"));
            $this->setVerificationErrors("Check 1: " . $etext);
        } else {
            //check for successful message
            if (!$this->waitForElement($this->getUiElement("admin/messages/success"), 30)) {
                $this->setVerificationErrors("Check 2 : No successfull message");
            }
            if (!$this->isElementPresent($this->getUiElement("admin/pages/system/configuration/tabs/web/elements/storedSecure", $this->_siteCode))) {
                $this->setVerificationErrors("Check 3 : Secure BaseUrl value wasn't saved");
            }
            if (!$this->isElementPresent($this->getUiElement("admin/pages/system/configuration/tabs/web/elements/storedUnsecure", $this->_siteCode))) {
                $this->setVerificationErrors("Check 4 : UnSecure BaseUrl value wasn't saved");
            }
        }
        Core::debug("adding URL finished");
     }
    /**
     * Admin-System-Configuration: Reindex Data
     *
     */
     public function doReindex() {
        Core::debug("Reindex started...");
        $this->clickAndWait($this->getUiElement("admin/topmenu/system/indexmanagement"));
        $this->click($this->getUiElement("admin/pages/system/reindex/buttons/selectall"));
        $this->click($this->getUiElement("admin/pages/system/reindex/buttons/submit"));
        $this->waitForElement($this->getUiElement("admin/messages/success"), 60);


        // check for error message
        if ($this->waitForElement($this->getUiElement("admin/messages/error"),1)) {
            $etext = $this->getText($this->getUiElement("admin/messages/error"));
            $this->setVerificationErrors("Check 1: " . $etext);
        } else {
        //check for successful message
            if (!$this->isElementPresent($this->getUiElement("admin/messages/success"))) {
                $this->setVerificationErrors("Check 2 : No successfull message");
            }
            if (!$this->isTextPresent($this->getUiElement("admin/pages/system/reindex/messages/allsuccess"))) {
                $this->setVerificationErrors("Check 3 : data wasn't reindexed");
            }
        }
        Core::debug("Reidex finished");
     }

}


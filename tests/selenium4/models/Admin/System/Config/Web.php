<?php
/**
 * Admin_User_Site model
 *
 * @author Magento Inc.
 */
class Model_Admin_System_Config_Web extends Model_Admin
{
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $params = array (
            "storeView" => Core::getEnvConfig('backend/scope/site/name')
        );
        $this->configURLData = $params;
    }

    /**
     * Admin-System-Configuration-Web: configurate base URL
     *
     */
     public function configURL($params = array()) {
        $this->printDebug('adding URL started');

        $systemConfig = $params ? $params : $this->systemConfig;
        $siteCode = $systemConfig['siteCode'];
        $siteName = $systemConfig['siteName'];
        $storeViewName = $systemConfig['storeViewName'];
        $this->printInfo($siteName . ' \ ' . $storeViewName);

        //Open SystemConfiguration
        $this->clickAndWait($this->getUiElement('/admin/topmenu/system/configuration'));
        //Select site
        $this->selectAndWait($this->getUiElement('/admin/pages/system/configuration/elements/store_selector'), $siteName);
        //Open web tab
        $this->clickAndWait($this->getUiElement('/admin/pages/system/configuration/links/web'));

        $this->setUiNamespace('/admin/pages/system/configuration/tabs/web/');
        //Open unSecure section
        if ($this->waitForElement($this->getUiElement('elements/hidedUnsecure'),1)) {
            $this->click($this->getUiElement('links/unsecure'));
        };
        //uncheck 'Usedefault' if neccessary
        if ($this->isElementPresent($this->getUiElement('unsecure_tab/checkboxes/useDefaultBaseLinkChecked'))) {
            $this->click($this->getUiElement('unsecure_tab/checkboxes/useDefaultBaseLink'));
        }
        //Add prefix to base-url
        $this->type($this->getUiElement('unsecure_tab/inputs/baselink'), '{{unsecure_base_url}}' . 'websites/' . $siteCode . '/');
        //Open secure section        
        if ($this->waitForElement($this->getUiElement('elements/hidedSecure'),1)) {
            $this->click($this->getUiElement('links/secure'));
        };
        //uncheck 'Usedefault' if neccessary
        if ($this->isElementPresent($this->getUiElement('secure_tab/checkboxes/useDefaultBaseLinkChecked'))) {
            $this->click($this->getUiElement('secure_tab/checkboxes/useDefaultBaseLink'));
        }
        //Add prefix to base-url
        $this->type($this->getUiElement('secure_tab/inputs/baselink'), '{{secure_base_url}}' . 'websites/' . $siteCode . '/');
        //Save Configuration
        $this->click($this->getUiElement('/admin/pages/system/configuration/buttons/save'));

        // check for error message
        if ($this->waitForElement($this->getUiElement('/admin/messages/error'),1)) {
            $etext = $this->getText($this->getUiElement('/admin/messages/error'));
            $this->setVerificationErrors('Check 1: ' . $etext);
        } else {
            //check for successful message
            if (!$this->waitForElement($this->getUiElement('/admin/messages/success'), 30)) {
                $this->setVerificationErrors('Check 2 : No successfull message');
            }
            if (!$this->isElementPresent($this->getUiElement('elements/storedSecure', 'websites/'.$siteCode))) {
                $this->setVerificationErrors("Check 3 : Secure BaseUrl value wasn't saved");
            }
            if (!$this->isElementPresent($this->getUiElement('elements/storedUnsecure', 'websites/'.$siteCode))) {
                $this->setVerificationErrors("Check 4 : UnSecure BaseUrl value wasn't saved");
            }
        }
        $this->printDebug('adding URL finished');
     }

    /**
     * Admin-System-Configuration: Reindex Data
     *
     */
     public function doReindex() {
        $this->printDebug('Reindex started...');
        $this->clickAndWait($this->getUiElement('/admin/topmenu/system/indexmanagement'));
        $this->click($this->getUiElement('/admin/pages/system/reindex/buttons/selectall'));
        $this->click($this->getUiElement('/admin/pages/system/reindex/buttons/submit'));
        $this->waitForElement($this->getUiElement('/admin/messages/success'), 120);

        // check for error message
        if ($this->waitForElement($this->getUiElement('/admin/messages/error'),1)) {
            $etext = $this->getText($this->getUiElement('/admin/messages/error'));
            $this->setVerificationErrors('Check 1: ' . $etext);
        } else {
        //check for successful message
            if (!$this->isElementPresent($this->getUiElement('/admin/messages/success'))) {
                $this->setVerificationErrors('Check 2 : No successfull message');
            }
            if (!$this->isTextPresent($this->getUiElement('/admin/pages/system/reindex/messages/allsuccess'))) {
                $this->setVerificationErrors("Check 3 : data wasn't reindexed");
            }
        }
        $this->printDebug('Reidex finished');
     }

}
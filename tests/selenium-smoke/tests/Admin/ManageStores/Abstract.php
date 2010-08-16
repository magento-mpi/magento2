<?php
/**
 * Abstract test class for Admin module
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_ManageStores_Abstract extends Test_Admin_Abstract
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
        $this->_siteOrder = Core::getEnvConfig('backend/managestores/site/sortorder');
    }

    /**
     * Admin-System-Store Management: New Site Creation
     *
     */

     public function adminSiteCreation($name, $code, $sortorder) {     
        $this->clickAndWait ($this->getUiElement("admin/topmenu/system/managestores/link/openpage"));
        $this->clickAndWait ($this->getUiElement("admin/pages/system/managestores/createsite/buttons/createwebsite"));
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/name"), $name);
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/code"), $code);
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/order"), $sortorder);
        $this->clickAndWait($this->getUiElement("admin/pages/system/managestores/createsite/buttons/save"));

        //check for successful message
        if ($this->isElementPresent($this->getUiElement("admin/pages/system/managestores/createsite/messages/saved"))) {
            $this->setVerificationErrors("Check 1 : Site was successfully created");

        }
        if ($this->isElementPresent($this->getUiElement("admin/pages/system/managestores/createsite/buttons/save"))) {
            $this->setVerificationErrors("Check 1 failed: website wasn't created");
    }
    }



}


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
        //$this->_storeviewSortOrder = Core::getEnvConfig('backend/managestores/storeview/order');
    }

    /**
     * Admin-System-Store Management: New Site Creation
     *
     */
     public function adminSiteCreation($sitename, $code, $sortorder)
    {
        Core::debug("adminSiteCreation started",7);

        //Open manage stores page
        $this->clickAndWait ($this->getUiElement("admin/topmenu/system/managestores/link/openpage"));

        //Create new site
        $this->clickAndWait ($this->getUiElement("admin/pages/system/managestores/createsite/buttons/createwebsite"));

        //Fill all fields
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/name"), $sitename);
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/code"), $code);
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/order"), $sortorder);

        //Press Save button
        $this->clickAndWait($this->getUiElement("admin/pages/system/managestores/createsite/buttons/save"));

        // check for error message
        if ($this->waitForElement($this->getUiElement("admin/pages/system/managestores/createsite/messages/error"),1)) {
            $etext = $this->getText($this->getUiElement("admin/pages/system/managestores/createsite/messages/error"));
            $this->setVerificationErrors("Check 1: " . $etext);
        } else {
        // Check for success message
            if (!$this->waitForElement($this->getUiElement("admin/pages/system/managestores/createsite/messages/success"),1)) {
            $this->setVerificationErrors("Check 2: no success message",7);
          }
        }
        Core::debug("adminSiteCreation finished");
     }

    /**
     * Admin-System-Store Management: New Store Creation
     * @param sitename
     * @param storename
     * @param rootname
     */
     public function adminStoreCreation($sitename, $storename, $rootname)
    {
        Core::debug("adminStoreCreation started",7);

        //Open manage stores page
        $this->clickAndWait ($this->getUiElement("admin/topmenu/system/managestores/link/openpage"));

        //Create webstore
        $this->clickAndWait ($this->getUiElement("admin/pages/system/managestores/createsite/buttons/createwebstore"));

        //Fill all fields
        $this->select($this->getUiElement("admin/pages/system/managestores/createsite/select/site"), $sitename);
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/storename"), $storename);
        $this->select($this->getUiElement("admin/pages/system/managestores/createsite/select/rootcategory"), $rootname);

        //Save store
        $this->clickAndWait($this->getUiElement("admin/pages/system/managestores/createsite/buttons/savestore"));

        // check for error message
        if ($this->waitForElement($this->getUiElement("admin/pages/system/managestores/createsite/messages/error"),1)) {
            $etext = $this->getText($this->getUiElement("admin/pages/system/managestores/createsite/messages/error"));
            $this->setVerificationErrors("Check 1: " . $etext);
        } else {
            // Check for success message
            if (!$this->waitForElement($this->getUiElement("admin/pages/system/managestores/createsite/messages/success"),1)) {
            $this->setVerificationErrors("Check 2: no success message",7);
          }
        }

        Core::debug("adminStoreCreation finished",7);
    }

    /**
     * Admin-System-Store Management: New StoreView Creation
     * @param storename
     * @param storeviewname
     * @param storeviewcode
     * @param storeviewstatus
     *
     */
     public function adminStoreViewCreation($storename, $storeviewname, $storeviewcode, $storeviewstatus)
     {
        Core::debug("adminStoreViewCreation started");

        //Open manage stores page
        $this->clickAndWait ($this->getUiElement("admin/topmenu/system/managestores/link/openpage"));

        //Create storeview
        $this->clickAndWait ($this->getUiElement("admin/pages/system/managestores/createsite/buttons/createstoreview"));

        //Fill all fields
        $this->select($this->getUiElement("admin/pages/system/managestores/createsite/select/store"), $storename);
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/storeviewname"), $storeviewname);
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/storeviewcode"), $storeviewcode);
        $this->select($this->getUiElement("admin/pages/system/managestores/createsite/select/storestatus"), $storeviewstatus);

        //Save storeview
        $this->clickAndWait($this->getUiElement("admin/pages/system/managestores/createsite/buttons/savestoreview"));

        // check for error message
        if ($this->waitForElement($this->getUiElement("admin/pages/system/managestores/createsite/messages/error"),1)) {
            $etext = $this->getText($this->getUiElement("admin/pages/system/managestores/createsite/messages/error"));
            $this->setVerificationErrors("Check 1: " . $etext);
        } else {
            // Check for success message
            if (!$this->waitForElement($this->getUiElement("admin/pages/system/managestores/createsite/messages/success"),1)) {
            $this->setVerificationErrors("Check 2: no success message",7);
          }
        }
        Core::debug("adminStoreViewCreation finished");
    }
}


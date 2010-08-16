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
        $this->_storeName = Core::getEnvConfig('backend/managestores/store/storename');
        $this->_rootCategoryName = Core::getEnvConfig('backend/managecategories/rootname');

        $this->_storeviewName = Core::getEnvConfig('backend/managestores/storeview/name');
        $this->_storeviewCode = Core::getEnvConfig('backend/managestores/storeview/code');
        $this->_storeviewStatus = Core::getEnvConfig('backend/managestores/storeview/status');
        //$this->_storeviewSortOrder = Core::getEnvConfig('backend/managestores/storeview/order');
    }

    /**
     * Admin-System-Store Management: New Site Creation
     *
     */

     public function adminSiteCreation($sitename, $code, $sortorder) {
        $this->clickAndWait ($this->getUiElement("admin/topmenu/system/managestores/link/openpage"));
        $this->clickAndWait ($this->getUiElement("admin/pages/system/managestores/createsite/buttons/createwebsite"));
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/name"), $sitename);
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/code"), $code);
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/order"), $sortorder);
        $this->clickAndWait($this->getUiElement("admin/pages/system/managestores/createsite/buttons/save"));

        //check for successful message
        if (!$this->isElementPresent($this->getUiElement("admin/pages/system/managestores/createsite/messages/saved"))) {
            $this->setVerificationErrors("Check 1 : No successfull message");

        }
     }
 /**
     * Admin-System-Store Management: New Store Creation
     *
     */
     public function adminStoreCreation($sitename, $storename, $rootname) {
        $this->clickAndWait ($this->getUiElement("admin/topmenu/system/managestores/link/openpage"));     
        $this->clickAndWait ($this->getUiElement("admin/pages/system/managestores/createsite/buttons/createwebstore"));
        $this->select($this->getUiElement("admin/pages/system/managestores/createsite/select/site"), $sitename);
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/storename"), $storename);
        $this->select($this->getUiElement("admin/pages/system/managestores/createsite/select/rootcategory"), $rootname);
        $this->clickAndWait($this->getUiElement("admin/pages/system/managestores/createsite/buttons/savestore"));

        //check for successful message
        if (!$this->isElementPresent($this->getUiElement("admin/pages/system/managestores/createsite/messages/saved"))) {
            $this->setVerificationErrors("Check 1 :  No successfull message");

        }       
    }
 /**
     * Admin-System-Store Management: New StoreView Creation
     *
     */
         public function adminStoreViewCreation($storename, $storeviewname, $storeviewcode, $storeviewstatus) {
        $this->clickAndWait ($this->getUiElement("admin/topmenu/system/managestores/link/openpage"));
        $this->clickAndWait ($this->getUiElement("admin/pages/system/managestores/createsite/buttons/createstoreview"));
        $this->select($this->getUiElement("admin/pages/system/managestores/createsite/select/store"), $storename);
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/storeviewname"), $storeviewname);
        $this->type($this->getUiElement("admin/pages/system/managestores/createsite/inputs/storeviewcode"), $storeviewcode);
        $this->select($this->getUiElement("admin/pages/system/managestores/createsite/select/storestatus"), $storeviewstatus);
        $this->clickAndWait($this->getUiElement("admin/pages/system/managestores/createsite/buttons/savestoreview"));

        //check for successful message
        if (!$this->isElementPresent($this->getUiElement("admin/pages/system/managestores/createsite/messages/saved"))) {
            $this->setVerificationErrors("Check 1 :  No successfull message");

        }       
    }
}


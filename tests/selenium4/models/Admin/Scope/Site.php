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
     * Create site
     *
     * @param array $params May contain the following params:
     * name, code, sortorder
     */
    public function doCreate($params = array())
    {
        $siteData = $params ? $params : $this->siteData;
        //Open ManageStores
        $this->clickAndWait(
            $this->getUiElement("/admin/topmenu/system/managestores/link/openpage")
        );
        $this->setUiNamespace('admin/pages/system/scope/site');
        $this->clickAndWait($this->getUiElement("/admin/pages/system/scope/manage_stores/buttons/create_web_site"));
        //Fill all fields
        $this->type($this->getUiElement("inputs/name"), $siteData['name']);
        $this->type($this->getUiElement("inputs/code"), $siteData['code']);
        $this->type($this->getUiElement("inputs/order"), $siteData['sortorder']);
        $this->clickAndWait($this->getUiElement("buttons/save"));

        // check for error message
        if ($this->waitForElement($this->getUiElement('/admin/messages/error'),1)) {
            $etext = $this->getText($this->getUiElement('/admin/messages/error'));
            $this->setVerificationErrors('doCreate: ' . $etext);
        } else {
          // Check for success message
          if ($this->waitForElement($this->getUiElement('/admin/messages/success'),1)) {
            $this->printInfo('Site ' . $siteData['code'] . ' has been created');
          } else {
            $this->setVerificationErrors('doCreate: no success message');
          }
        }

    }

    /**
     * Delete site
     *
     * @param array $params May contain the following params:
     * name, code
     */
    public function doDelete($params = array())
    {
        $this->printDebug('doDelete started');
        $siteData = $params ? $params : $this->siteData;
        $name = $this->siteData['name'];
        $code = $this->siteData['code'];

        $this->clickAndWait(
            $this->getUiElement("/admin/topmenu/system/managestores/link/openpage")
        );


        if ($this->doOpen($name)) {
            $this->setUiNamespace('/admin/pages/system/scope/site');
            //Delete site
            $this->clickAndWait($this->getUiElement('buttons/delete'));
            //Select No backup
            $this->waitForElement($this->getUiElement('/admin/pages/system/scope/create_backup/selectors/create_backup'),5);
            $this->select($this->getUiElement('/admin/pages/system/scope/create_backup/selectors/create_backup'),'label=No');
            //Delete Site
            $this->click($this->getUiElement('buttons/delete'));
            $this->waitForElement($this->getUiElement('/admin/pages/system/scope/manage_stores/elements/store_table'),130);

            // check for error message
            if ($this->waitForElement($this->getUiElement('/admin/messages/error'),1)) {
                $etext = $this->getText($this->getUiElement('/admin/messages/error'));
                $this->setVerificationErrors('doDelete: ' . $etext);
            } else {
              // Check for success message
              if ($this->waitForElement($this->getUiElement('/admin/messages/success'),1)) {
                $this->printInfo('Site ' . $code . ' has been deleted');
              } else {
                $this->setVerificationErrors('doDelete: no success message');
              }
            }
        }
        $this->printDebug('doDelete finished...');
    }

    /**
     * Open site from admin
     * @param name, code
     * @return boolean
     */
    public function doOpen($params = array())
    {
        $this->printDebug('doOpen started');
        $userData = $params ? $params : $this->siteData;
        $name = $this->siteData['name'];
        $code = $this->siteData['code'];
        $this->setUiNamespace('/admin/pages/system/scope/manage_stores');
        //Open ManageStores
        $this->clickAndWait(
            $this->getUiElement("/admin/topmenu/system/managestores/link/openpage")
        );

        // Filter users by name
        $this->click($this->getUiElement('buttons/reset_filter'));
        sleep(1);
        $this->waitForElement($this->getUiElement('filters/site_name'),5);
        //Filter by username
        $this->type($this->getUiElement('filters/site_name'),$name);
        $this->waitForElement($this->getUiElement('buttons/search'),5);
        $this->click($this->getUiElement('buttons/search'));
        sleep(1);
        //Open user with 'User Name' == name
        //Determine Column with 'User Name' title
        $this->waitForElement($this->getUiElement('filters/site_name'),5);
        $result = $this->findRightSite($this->getUiElement('elements/store_table'), $name, $code);
        $this->setUiNamespace('/admin/pages/system/scope/manage_stores');
        if ($result > -1 ) {
            $this->clickAndWait($this->getUiElement('elements/body') . '//tr['. $result .']/td[1]//a');
            $this->printInfo('Site ' . $code . ' opened');
            return true;
        } else {
          $this->printDebug('doOpenSite finished with false');
          return false;
        }
    }

    /*
     * Sequeantally open all sites with $name
     * check condition opened_site_name == $name and opened_site_code == $code
     * @param $name
     * @param $code
     * @return rowIndex with right site
     */
    public function findRightSite($tableXPath, $name, $code)
    {
        $result = -1;
        $this->printDebug('findRightStore Started...');
        $rowNum = $this->getXpathCount($tableXPath . '//tbody//tr');
        $this->setUiNamespace('/admin/pages/system/scope/site');
//        $this->printDebug($rowNum);
        for ($row=0; $row<=$rowNum-1; $row++) {
            $cellLocator = $tableXPath . '//tbody' . '.' . $row . '.' . 0;
            $this->waitForElement($tableXPath . '//tbody', 5);
            $cell = $this->getTable($cellLocator);
            if ($cell == $name) {
                $valueRowInd = $row + 1;
//                $this->printDebug('Founded in ' . $cellLocator);
                //
//                $this->printDebug('click=' . $tableXPath . '//tbody//tr' . '[' . $valueRowInd . ']/td[1]//a');
//                $this->printDebug('Open site for edit...');
                $this->clickAndWait($tableXPath . '//tbody//tr' . '[' . $valueRowInd . ']/td[1]//a');
                sleep(1);
                // Check Name and Code values
//                $this->printDebug('NameChecker=' . $this->getUiElement('elements/website_name_value',$name));
//                $this->printDebug('CodeChecker=' . $this->getUiElement('elements/website_code_value',$code));
                if (($this->isElementPresent($this->getUiElement('elements/website_name_value',$name))) and
                   ($this->isElementPresent($this->getUiElement('elements/website_code_value',$code)))) {
                    $result = $valueRowInd;
                    $this->printDebug('Site ' . $name . ' and ' . $code . ' founded!');
               }
               $this->printDebug('Return back to site list...');
               $this->clickAndWait($this->getUiElement('buttons/back'));
               if ($result>-1) {
                   break;
               }
            }
//            $this->printDebug($cellLocator . ' == [' . $cell .']');
        }
        $this->printDebug('findRightStore finished with result = ' . $result);
        return $result;
    }

}

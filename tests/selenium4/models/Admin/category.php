<?php
/**
 * Admin_Scope_Site model
 *
 * @author Magento Inc.
 */
class Model_Admin_Category extends Model_Admin
{
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->siteData = Core::getEnvConfig('backend/managecategories');
    }

    /**
     * Adds new  $subcategoryname for $rootname
     *@param $rootname
     *@param $subcategoryname
     *
     */
    public function doAddSubCategory($params = array())
    {
        $result = true;
        $siteData = $params ? $params : $this->siteData;

        // Open Manage Categories Page
        $this->clickAndWait ($this->getUiElement("/admin/topmenu/catalog/categories/managecategories"));
        $this->model->pleaseWait();

        $this->setUiNamespace('admin/pages/catalog/categories/managecategories');

        //Select Parent Category
        $this->click($this->getUiElement("locators/parentcategory",$siteData['rootname']));
        $this->model->pleaseWait();
        // Add new sub category
        $this->click($this->getUiElement("buttons/addsubcategory"));
        $this->model->pleaseWait();
        // Fill all fields
        $this->type($this->getUiElement("inputs/name"),$siteData['subcategoryname']);
        $this->select($this->getUiElement("selectors/isactive"),"label=Yes");
        $this->click($this->getUiElement("tabs/displaysettings"));
        $this->select($this->getUiElement("selectors/isanchor"),"label=Yes");
        // Save category
        $this->clickAndWait($this->getUiElement("buttons/savecategory"));
        $this->model->pleaseWait();
        // check for error message
        if ($this->isElementPresent($this->getUiElement("messages/error"))) {
            $etext = $this->getText($this->getUiElement("messages/error"));
            $this->setVerificationErrors("Check 1: " . $etext);
        } else {
        // Check for success message
        if (!$this->isElementPresent($this->getUiElement("messages/categorysaved"))) {
            $this->setVerificationErrors("Check 2: no success message");
        }
        }
        if ($result) {
            $this->printInfo('Sub category created');
        }
        return $result;
    }
        
    /**
     * Adds new root $rootname category into the $storeViewName store view
     *@param $rootname
     *@param $storeViewName
     *
     */
    public function doAddRootCategory($params = array())
    {
        $result = true;
        $siteData = $params ? $params : $this->siteData;

        // Open Manage Categories Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/categories/managecategories"));
        $this->model->pleaseWait();

        $this->setUiNamespace('admin/pages/catalog/categories/managecategories');

        // Add new root category
        $this->click($this->getUiElement("buttons/addrootcategory"));
        $this->model->pleaseWait();
        // Fill all fields
        $this->type($this->getUiElement("inputs/name"),$siteData['rootname']);
        $this->select($this->getUiElement("selectors/isactive"),"label=Yes");
        // Save category
        $this->click($this->getUiElement("buttons/savecategory"));
        $this->model->pleaseWait();
        // check for error message
        if ($this->isElementPresent($this->getUiElement("messages/error"))) {
            $etext = $this->getText($this->getUiElement("messages/error"));
            $this->setVerificationErrors("Check 1: " . $etext);
            $result = false;
        } else {
        // Check for success message
        if (!$this->isElementPresent($this->getUiElement("messages/categorysaved"))) {
            $this->setVerificationErrors("Check 2: no success message");
        }
        }
        if ($result) {
            $this->printInfo('Root category created');
        }
        return $result;
    }

    /**
     * Delete root category $rootname
     *@param $rootname
     *
     */
    public function doDeleteRootCategory($params = array())
    {
        $result = true;
        $siteData = $params ? $params : $this->siteData;

        // Open Manage Categories Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/categories/managecategories"));

        $this->setUiNamespace('admin/pages/catalog/categories/managecategories');

        //Select Root Category
        $this->click($this->getUiElement("locators/parentcategory",$siteData['rootname']));
        $this->model->pleaseWait();
        //Verify that you can delete a root category
        if (!$this->isElementPresent($this->getUiElement("buttons/delete_category"))) {
            $this->setVerificationErrors("You cannot remove a category");
            $result = false;
        } else {
            $this->chooseOkOnNextConfirmation();
            $this->click($this->getUiElement("buttons/delete_category"));
            $this->model->pleaseWait();        
        // Check for success message
        if (!$this->isElementPresent($this->getUiElement("messages/categorysaved"))) {
            $this->setVerificationErrors("Check 2: no success message");
        }
        if ($result) {
            $this->printInfo('Root category deleted');
        }
        }
        return $result;
    }
}
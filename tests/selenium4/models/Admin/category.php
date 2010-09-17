<?php
/**
 * Model_Admin_Category model
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

        $this->categoryData = Core::getEnvConfig('backend/managecategories');
    }

    /**
     * Adds new sub category for root category
     *@param $params = array()
     *
     */
    public function doAddSubCategory($params = array())
    {
        $result = true;
        $categoryData = $params ? $params : $this->categoryData;

        // Open Manage Categories Page
        $this->clickAndWait ($this->getUiElement("/admin/topmenu/catalog/categories/managecategories"));
        $this->model->pleaseWait();

        $this->setUiNamespace('admin/pages/catalog/categories/managecategories');

        //Select Parent Category
        $this->click($this->getUiElement("locators/parentcategory",$categoryData['rootname']));
        $this->model->pleaseWait();
        // Add new sub category
        $this->click($this->getUiElement("buttons/addsubcategory"));
        $this->model->pleaseWait();
        // Fill all fields
        $this->type($this->getUiElement("inputs/name"),$categoryData['subcategoryname']);
        $this->select($this->getUiElement("selectors/isactive"),"label=Yes");
        $this->click($this->getUiElement("tabs/displaysettings"));
        $this->select($this->getUiElement("selectors/isanchor"),"label=Yes");
        // Save category
        $this->clickAndWait($this->getUiElement("buttons/savecategory"));
        $this->model->pleaseWait();
        // check for error message
        if ($this->isElementPresent($this->getUiElement("messages/error"))) {
            $etext = $this->getText($this->getUiElement("messages/error"));
            $this->setVerificationErrors($etext);
            $result = false;
        } else {
        // Check for success message
        if (!$this->isElementPresent($this->getUiElement("messages/categorysaved"))) {
            $this->setVerificationErrors("Check 2: no success message");
            $result = false;
        }
        }
        if ($result) {
            $this->printInfo('Sub category created');
        }
        return $result;
    }
        
    /**
     * Adds new root category
     *$@param $params = array()
     *
     */
    public function doAddRootCategory($params = array())
    {
        $result = true;
        $categoryData = $params ? $params : $this->categoryData;

        // Open Manage Categories Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/categories/managecategories"));
        $this->model->pleaseWait();

        $this->setUiNamespace('admin/pages/catalog/categories/managecategories');

        // Add new root category
        $this->click($this->getUiElement("buttons/addrootcategory"));
        $this->model->pleaseWait();
        // Fill all fields
        $this->type($this->getUiElement("inputs/name"),$categoryData['rootname']);
        $this->select($this->getUiElement("selectors/isactive"),"label=Yes");
        // Save category
        $this->click($this->getUiElement("buttons/savecategory"));
        $this->model->pleaseWait();
        // check for error message
        if ($this->isElementPresent($this->getUiElement("messages/error"))) {
            $etext = $this->getText($this->getUiElement("messages/error"));
            $this->setVerificationErrors($etext);
            $result = false;
        } else {
        // Check for success message
        if (!$this->isElementPresent($this->getUiElement("messages/categorysaved"))) {
            $this->setVerificationErrors("Check 2: no success message");
            $result = false;
        }
        }
        if ($result) {
            $this->printInfo('Root category created');
        }
        return $result;
    }

    /**
     * Delete root category $rootname
     *@param $params = array()
     *
     */
    public function doDeleteRootCategory($params = array())
    {
        $result = true;
        $categoryData = $params ? $params : $this->categoryData;

        // Open Manage Categories Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/categories/managecategories"));

        $this->setUiNamespace('admin/pages/catalog/categories/managecategories');
        //Select Root Category
        $this->click($this->getUiElement("locators/parentcategory",$categoryData['rootname']));
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
            $result = false;
        }
        if ($result) {
            $this->printInfo('Root category deleted');
        }
        }
    }

        /**
         * Delete sub category 
         *@param $params = array()
         *
         */
        public function doDeleteSubCategory($params = array())
    {
        $result = true;
        $categoryData = $params ? $params : $this->categoryData;

        // Open Manage Categories Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/categories/managecategories"));

        $this->setUiNamespace('admin/pages/catalog/categories/managecategories');
        //Select Sub Category
        $this->click($this->getUiElement("locators/parentcategory",$categoryData['subcategoryname']));
        $this->model->pleaseWait();
        //delete a sub category
        $this->chooseOkOnNextConfirmation();
        $this->click($this->getUiElement("buttons/delete_category"));
        $this->model->pleaseWait();
        // Check for success message
        if (!$this->isElementPresent($this->getUiElement("messages/categorysaved"))) {
            $this->setVerificationErrors("Check 2: no success message");
            $result = false;
        }
        if ($result) {
            $this->printInfo('Sub category deleted');
        }
        
    }

}
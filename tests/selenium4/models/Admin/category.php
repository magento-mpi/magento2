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
        $this->click($this->getUiElement("locators/root_category",$categoryData['rootname']));
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
        //Determining the number of root categories which contains
        $qtyRoot = $this->getXpathCount($this->getUiElement("locators/root_category",$categoryData['rootname']));
        if ($qtyRoot == 1) {
        //Select Root Category
        $this->click($this->getUiElement("locators/root_category",$categoryData['rootname']));
        $this->model->pleaseWait();
        //Verify that you can delete a root category
        if (!$this->isElementPresent($this->getUiElement("buttons/delete_category"))) {
            $newName = 'Renamed_Root_Category';
            $nameRoot = $this->getText($this->getUiElement("locators/root_category",$categoryData['rootname']));
            $nameRoot = strstr(strrev($nameRoot), ' ');
            $nameRoot = substr(strrev($nameRoot), 0, -1);
        if ($categoryData['rootname'] == $nameRoot){
            $this->printInfo("You cannot remove '".$nameRoot."' category.It will be renamed to the '".$newName."'");
            $this->type($this->getUiElement("inputs/name"),$newName);
            $this->click($this->getUiElement("buttons/savecategory"));
            $this->model->pleaseWait();
        } else {
        $this->printInfo("You try to delete the wrong root category");
        }
        } else {
            $this->click($this->getUiElement("buttons/delete_category"));
            $this->assertConfirmation('Are you sure you want to delete this category?');
            $this->chooseOkOnNextConfirmation();
            $this->model->pleaseWait();
        // Check for success message
        if (!$this->isElementPresent($this->getUiElement("messages/categorysaved"))) {
            $this->setVerificationErrors("Check 2: no success message");
            $result = false;
        }
        if ($result) {
            $this->printInfo("'".$categoryData['rootname']. "' root category deleted");
        }
        }
        } 
        if ($qtyRoot == 0) {
            $this->printInfo("Deletion '".$categoryData['rootname']. "' categoty: There is no necessary root category");
        }
        if ($qtyRoot >= 2) {
            $numNeededCat = array ();
        for ($i=1; $i<=$qtyRoot; $i++) {
            $mas = array ($categoryData['rootname'],$i);
            $nameRoot = $this->getText($this->getUiElement("locators/root_many",$mas));
            $nameRoot = strstr(strrev($nameRoot), ' ');
            $nameRoot = substr(strrev($nameRoot), 0, -1);
        if ($nameRoot == $categoryData['rootname']){
            $numNeededCat[]= "$i";
        }
        }
        $qtyRoot = count($numNeededCat);
        if ($qtyRoot == 1) {
            $mas = array ($categoryData['rootname'],$numNeededCat[0]);
            $this->click($this->getUiElement("locators/root_many",$mas));
            $this->model->pleaseWait();
        //Verify that you can delete a root category
        if (!$this->isElementPresent($this->getUiElement("buttons/delete_category"))) {
            $newName = 'Renamed_Root_Category';
            $this->printInfo("You cannot remove '".$categoryData['rootname']."' category.It will be renamed to the '".$newName."'");
            $this->type($this->getUiElement("inputs/name"),$newName);
            $this->click($this->getUiElement("buttons/savecategory"));
            $this->model->pleaseWait();
        } else{
            $this->click($this->getUiElement("buttons/delete_category"));
            $this->assertConfirmation('Are you sure you want to delete this category?');
            $this->chooseOkOnNextConfirmation();
            $this->model->pleaseWait();
        // Check for success message
        if (!$this->isElementPresent($this->getUiElement("messages/categorysaved"))) {
            $this->setVerificationErrors("Check 2: no success message");
            $result = false;
        }
        if ($result) {
            $this->printInfo("'".$categoryData['rootname']. "' root category deleted");
        }
        }
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
        $this->click($this->getUiElement("locators/sub_category",$categoryData['subcategoryname']));
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
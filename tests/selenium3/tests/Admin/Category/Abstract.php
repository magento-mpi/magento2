<?php
/**
 * Abstract test class derived from PHPUnit_Extensions_SeleniumTestCase
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_Category_Abstract extends Test_Admin_Abstract
{
    public function doFillTextField($tableBaseURL,$fieldName, $fieldValue) {
        //echo ("\ndoFillTextField:".$tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input = ".$fieldValue);
        $this->type($tableBaseURL . "//tr[contains(td,'" . $fieldName . "')]/td[contains(@class,'value')]/input", $fieldValue);
    }

    public function doFillSelectField($tableBaseURL,$fieldName, $fieldValue) {
        //echo ("\ndoFillSelectField:".$tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/select = ".$fieldValue);
        $this->select($tableBaseURL . "//tr[contains(td,'" . $fieldName . "')]/td[contains(@class,'value')]/select", $fieldValue);
    }

    function doAddCategory($parentdirname, $dirname) {
        $this->click("//div[contains(@class,'nav-bar')]//a[contains(span,'Manage Categories')]");
        $this->waitForPageToLoad("95000");

        if ($this->waitForElement($this->getUiElement("containerNewCategory"), 30)) {
            if ($this->waitForElement($this->getUiElement("treeCategoriesList") . "//a[contains(span,'" . $parentdirname . "')]", 30)) {
                $this->click($this->getUiElement("treeCategoriesList") . "//a[contains(span,'" . $parentdirname . "')]");
                $this->pleaseWait();
                $this->click($this->getUiElement("btnAddSubCategory"));
                $this->pleaseWait();
                $this->doFillTextField($this->getUiElement("TableGeneralInformation"), "Name", $dirname);
                $this->doFillSelectField( $this->getUiElement("TableGeneralInformation"), "Is Active", "label=Yes");
                $this->waitForElement($this->getUiElement("btnSaveCategory"), 50);
                $this->clickAndWait($this->getUiElement("btnSaveCategory"));
                $this->pleaseWait();
                if (!$this->doWaitForElement($this->getUiElement("msgCategorySaved"), 30)) {
                    $this->setVerificationErrors("Category does not saved");
                }

            } else {
                $this->setVerificationErrors("Parent folder does not exist");
            }

        } else {
            $this->setVerificationErrors("Category Page does not loaded");
        }
    }

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp()
    {
        parent::setUp();
    }

}


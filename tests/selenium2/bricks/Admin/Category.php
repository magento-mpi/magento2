<?php

class Helper_Admin_Category extends Helper_Admin {
    protected $_uiMap = array(
        "containerNewCategory"    => "//div[@id='category-edit-container']//div[contains(h3,'New Category')]",
        "TableGeneralInformation" => "//div[@id='category_tab_content']/div[not(contains(@style,'display: none'))]//table",
        "treeCategoriesList"      => "//div[contains(@class,'tree-holder')]",
        "btnAddSubCategory"       => "//div[contains(@class,'categories-side-col')]//button[contains(span,'Add Subcategory')]",
        "btnSaveCategory"         => "//div[@id='category-edit-container']//button[contains(span,'Save Category')]",
        "msgCategorySaved"        => "//div[@id='messages']//*[contains(text(),'category has been saved')]"
    );

    public function fillTextField($tableBaseURL,$fieldName, $fieldValue) {
        //echo ("\nFillTextField:".$tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input = ".$fieldValue);
        $this->_context->type($tableBaseURL . "//tr[contains(td,'" . $fieldName . "')]/td[contains(@class,'value')]/input", $fieldValue);
    }

    public function fillSelectField($tableBaseURL,$fieldName, $fieldValue) {
        //echo ("\nFillSelectField:".$tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/select = ".$fieldValue);
        $this->_context->select($tableBaseURL . "//tr[contains(td,'" . $fieldName . "')]/td[contains(@class,'value')]/select", $fieldValue);
    }

    function addCategory($parentdirname, $dirname) {
        $this->_context->click("//div[contains(@class,'nav-bar')]//a[contains(span,'Manage Categories')]");
        $this->_context->waitForPageToLoad("95000");

        if ($this-> waitForElementNsec($this->getUiElement("containerNewCategory"), 30)) {
            if ($this-> waitForElementNsec($this->getUiElement("treeCategoriesList") . "//a[contains(span,'" . $parentdirname . "')]", 30)) {
                $this->_context->click($this->getUiElement("treeCategoriesList") . "//a[contains(span,'" . $parentdirname . "')]");
                $this->pleaseWait();
                $this->_context->click($this->getUiElement("btnAddSubCategory"));
                $this->pleaseWait();
                $this->fillTextField($this->getUiElement("TableGeneralInformation"), "Name", $dirname);
                $this->fillSelectField( $this->getUiElement("TableGeneralInformation"), "Is Active", "label=Yes");
                $this-> waitForElementNsec($this->getUiElement("btnSaveCategory"), 50);
                $this->_context->clickAndWait($this->getUiElement("btnSaveCategory"));
                $this->pleaseWait();
                if (!$this-> waitForElementNsec($this->getUiElement("msgCategorySaved"), 30)) {
                    $this->_context->setVerificationErrors("Category does not saved");
                }

            } else {
                $this->_context->setVerificationErrors("Parent folder does not exist");
            }

        } else {
            $this->_context->setVerificationErrors("Category Page does not loaded");
        }
    }
}

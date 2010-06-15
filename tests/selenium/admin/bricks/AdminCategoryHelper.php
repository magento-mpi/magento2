<?php

class AdminCategoryHelper extends AdminHelper {
    public $UIMap = array(
            "containerNewCategory"=>"//div[@id='category-edit-container']//div[contains(h3,'New Category')]",
            "TableGeneralInformation" => "//div[@id='category_tab_content']/div[not(contains(@style,'display: none'))]//table",
            "treeCategoriesList" => "//div[contains(@class,'tree-holder')]",
            "btnAddSubCategory" => "//div[contains(@class,'categories-side-col')]//button[contains(span,'Add Subcategory')]",
            "btnSaveCategory" => "//div[@id='category-edit-container']//button[contains(span,'Save Category')]",
            "msgCategorySaved" => "//div[@id='messages']//*[contains(text(),'category has been saved')]"
    );

    public function fillTextField($tableBaseURL,$fieldName, $fieldValue) {
        //echo ("\nFillTextField:".$tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input = ".$fieldValue);
        $this->_object->type($tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input", $fieldValue);
    }

    public function fillSelectField($tableBaseURL,$fieldName, $fieldValue) {
        //echo ("\nFillSelectField:".$tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/select = ".$fieldValue);
        $this->_object->select($tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/select", $fieldValue);
    }

    function addCategory($parentdirname, $dirname) {
        $this->_object->click("//div[contains(@class,'nav-bar')]//a[contains(span,'Manage Categories')]");
        $this->_object->waitForPageToLoad("95000");

        if ($this-> waitForElementNsec($this->UIMap["containerNewCategory"],30)) {
            if ($this-> waitForElementNsec($this->UIMap["treeCategoriesList"]."//a[contains(span,'".$parentdirname."')]",30)) {
                $this->_object->click($this->UIMap["treeCategoriesList"]."//a[contains(span,'".$parentdirname."')]");
                $this->pleaseWait();
                $this->_object->click($this->UIMap["btnAddSubCategory"]);
                $this->pleaseWait();
                $this->fillTextField( $this->UIMap["TableGeneralInformation"],"Name",$dirname);
                $this->fillSelectField( $this->UIMap["TableGeneralInformation"],"Is Active","label=Yes");
                $this-> waitForElementNsec($this->UIMap["btnSaveCategory"],50);
                $this->_object->clickAndWait($this->UIMap["btnSaveCategory"]);
                $this->pleaseWait();
                if (!$this-> waitForElementNsec($this->UIMap["msgCategorySaved"],30)) {
                    $this->_object->setVerificationErrors("Category does not saved");
                }

            } else {
                $this->_object->setVerificationErrors("Parent folder does not exist");
            }

        } else {
            $this->_object->setVerificationErrors("Category Page does not loaded");
        }
    }
}

?>
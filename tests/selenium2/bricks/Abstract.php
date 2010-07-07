<?php

class Helper_Abstract {
    /**
     * TestCase instance
     *
     * @var Test_Abstract
     */
    protected $_context;

    /**
     * UIMap container
     *
     * @var array
     */
    protected  $_uiMap = array
        (
            "containerNewCategory"=>"//div[@id='category-edit-container']//div[contains(h3,'New Category')]",
            "TableGeneralInformation" => "//div[@id='category_tab_content']/div[not(contains(@style,'display: none'))]//table",
            "treeCategoriesList" => "//div[contains(@class,'tree-holder')]",
            "btnAddSubCategory" => "//div[contains(@class,'categories-side-col')]//button[contains(span,'Add Subcategory')]",
            //"btnSaveCategory" => "//div[@id='category-edit-container']//button[contains(span,'Save Category')]",
            "btnSaveCategory" => "//button[contains(span,'Save Category')]",
            "msgCategorySaved" => "//div[@id='messages']//*[contains(text(),'category has been saved')]",
            "NewAddrFieldsTable" =>  "//div[@id='address_form_container']//div[contains(@id,'form_new_item')     and not(contains(@style,'display: none'))]//table[contains(@class,'form-list')]/tbody",
            "EditAddrFieldsTable" => "//div[@id='address_form_container']//div[contains(@id,'form_address_item') and not(contains(@style,'display: none'))]//table[contains(@class,'form-list')]/tbody",
            "AddrManagePanel" => "//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(@class,'on')]",
            //
            "admin/customer/button/savecustomer" => "//div[contains(@id,'page:main-container')]//div[contains(@class,'content-header')]//p[contains(@class,'form-buttons')]//button[contains(span,'Save Customer')]",
            "admin/customer/message/customersaved" => "The customer has been saved.",
            "admin/topmenu/customer/managecustomers" => "//div[@class=\"nav-bar\"]//a[span=\"Manage Customers\"]",
            "admin/customer/button/search" => "//div[@id='customerGrid']//button[span='Search']"
        );

    /**
     * Constructor
     * Initialize a TestCase context
     */

    public function updateContex() {
        $this->_context = Core::getContext();
    }

    /**
     * Constructor
     * Initialize a TestCase context
     */
    public function  __construct() {
        $this-> updateContex();
    }

    /**
     * Fetch an Xpath to access a certain UI element
     *
     * @param string $elem
     * @return string
     */
    public function getUiElement($elem)
    {
        return isset($this->_uiMap[$elem]) ? $this->_uiMap[$elem] : null;
    }

      /**
     * Wait of appearance of html element with Xpath during timeforwait sec
     *
     * @param string $xpath
     * @param int $timeforwait
     * @return boolean
     */
    public function waitForElementNsec($xpath, $timeforwait) {
        $res = false;
        for ($second = 0; ; $second++) {
            if ($second >= $timeforwait) {
                $this->_context->fail("element could not be founded :".$xpath);
            }
            try {
                if ($this->_context->isElementPresent($xpath)) {
                    $res=true;
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }
        return $res;
    }
}
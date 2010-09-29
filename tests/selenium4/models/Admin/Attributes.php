<?php

/**
 * Admin_Scope_Site model
 *
 * @author Magento Inc.
 */
class Model_Admin_Attributes extends Model_Admin {

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData() {
        parent::loadConfigData();

        //<!-- This array Data For Positiv Tests -->
/*        $this->Data = array(
            'attrribute_type_all'               => array(
                                        'Text Field',
                                        'Text Area',
                                        'Date',
                                        'Yes/No',
                                        'Multiple Select',
                                        'Dropdown',
                                        'Price',
                                        'Media Image',
                                        'Fixed Product Tax'
                                        ),
            'scope_all'                         => array('Store View', 'Website', 'Global'),
            'apply_to_all'                      => array('Selected Product Types', 'All Product Types'),
            'input_validation_all'              => array(
                                        'None',
                                        'Decimal Number',
                                        'Integer Number',
                                        'Email',
                                        'URL',
                                        'Letters',
                                        'Letters (a-z, A-Z) or Numbers (0-9)'
                                        ),
            'use_in_layered_navigation_all'     => array(
                                        'Filterable (with results)',
                                        'Filterable (no results)',
                                        'No'
                                        ),
            'attrribute_type'                   => 'Multiple Select',
            'attrribute_code'                   => 'attrribute_' . $this->getStamp(),
            'scope'                             => 'Global',
            'apply_to'                          => 'All Product Types',
            'input_validation'                  => 'Decimal Number',
            'use_in_layered_navigation'         => 'Filterable (no results)',
            'unique_value'                      => 'Yes',
            'values_required'                   => 'Yes',
            'use_to_create_configurable'        => 'Yes',
            'use_in_quick_search'               => 'Yes',
            'use_in_advanced_search'            => 'Yes',
            'comparable'                        => 'Yes',
            'use_in_search_results'             => 'Yes',
            'wysiwyg_enable'                    => 'Yes',
            'html_allowed_on_front'             => 'Yes',
            'visible_on_front'                  => 'Yes',
            'use_in_product_listing'            => 'Yes',
            'use_for_sort_by'                   => 'Yes',
            'position'                          => rand(1, 10),
            'default_value_yesno'               => 'Yes',
            'default_value_date'                => '09/23/10',
            'default_value_textarea'            => 'Text Area',
            'default_value_text'                => 'Text Field',
            'use_for_promo_rules'               => 'Yes',
            'attribute_admin_title'             => 'Admin Title',
            'attribute_store_view_title'        => 'Store View Title',
            'store_view_name'                   => 'Default Store View',
            'attribute_admin_option_name'       => array(
                                        'Simple Product',
                                        'Virtual Product',
                                        'Downloadable Product',
                                        'Grouped Product',
                                        'Bundle Product',
                                        'Configurable Product',
                                        'Gift card'
                                        ),
            'attribute_admin_option_position'   => array(1, 2, 3, 4, 5, 6, 7),
            'attribute_store_view_option_name'  => array(
                                        'Simple Product_store_view',
                                        'Virtual Product_store_view',
                                        'Downloadable Product_store_view',
                                        'Grouped Product_store_view',
                                        'Bundle Product_store_view',
                                        'Configurable Product_store_view',
                                        'Gift card_store_view'
                                        ),
        );*/

        //<!-- This array Data For Negative Tests -->
        $this->Data = array(
            'attrribute_type'                   => 'Dropdown',
            'attrribute_code'                   => '',
            'default_value_date'                => 'TEXT',
            'attribute_admin_title'             => '',
            'store_view_name'                   => 'Default Store View',
            'attribute_admin_option_name'       => array(),
            'attribute_admin_option_position'   => array('Text'),
        );
    }

    /**
     * Fill Attribute Properties Tab on attribute page
     *
     * @param array $params May contain the following params:
     * attrribute_type,
     */
    public function fillAttributePropertiesTab($params = array())
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attributes/attribute');
        // open Properties Tab
        $this->click($this->getUiElement("tabs/properties"));
        //Catalog Input Type for Store Owner
        $this->checkAndSelectField("attrribute_type", NULL);
        //Attribute Code
        $this->checkAndFillField("attrribute_code", NULL);
        if ($Data["attrribute_type"] != 'Fixed Product Tax' and $Data["attrribute_type"] != 'Price') {
            //Scope
            $this->checkAndSelectField("scope", NULL);
        }
        if ($Data["attrribute_type"] == 'Text Field') {
            //Input Validation for Store Owner
            $this->checkAndSelectField("input_validation", NULL);
            //Allow HTML Tags on Frontend
            $this->checkAndSelectField("html_allowed_on_front", NULL);
        }
        //Default Value
        if ($Data["attrribute_type"] == 'Date') {
            $this->checkAndFillField("default_value_date", NULL);
        }
        if ($Data["attrribute_type"] == 'Text Area') {
            $this->checkAndFillField("default_value_textarea", NULL);
        }
        if ($Data["attrribute_type"] == 'Text Field') {
            $this->checkAndFillField("default_value_text", NULL);
        }
        if ($Data["attrribute_type"] == 'Yes/No') {
            $this->checkAndSelectField("default_value_yesno", NULL);
        }
        if ($Data["attrribute_type"] == 'Dropdown' and
                isset($Data["scope"]) and
                $Data["scope"] == 'Global') {
            //Use To Create Configurable Product
            $this->checkAndSelectField("use_to_create_configurable", NULL);
        }
        if ($Data["attrribute_type"] != 'Fixed Product Tax' and $Data["attrribute_type"] != 'Media Image') {
            //Unique Value
            $this->checkAndSelectField("unique_value", NULL);
            //Values Required
            $this->checkAndSelectField("values_required", NULL);
            //Use in Quick Search
            $this->checkAndSelectField("use_in_quick_search", NULL);
            //Use in Advanced Search
            $this->checkAndSelectField("use_in_advanced_search", NULL);
            //Comparable on Front-end
            $this->checkAndSelectField("comparable", NULL);
            if ($Data["attrribute_type"] == 'Multiple Select' or
                    $Data["attrribute_type"] == 'Dropdown' or
                    $Data["attrribute_type"] == 'Price') {
                //Use In Layered Navigation
                $this->checkAndSelectField("use_in_layered_navigation", NULL);
                //Use In Search Results Layered Navigation
                $this->checkAndSelectField("use_in_search_results", NULL);
                if (isset($Data["use_in_layered_navigation"]) and $Data["use_in_layered_navigation"] != 'No') {
                    //Position
                    $this->checkAndFillField("position", NULL);
                }
            }
            //Use for Promo Rule Conditions
            $this->checkAndSelectField("use_for_promo_rules", NULL);
            //Visible on Product View Page on Front-end
            $this->checkAndSelectField("visible_on_front", NULL);
            //Used in Product Listing
            $this->checkAndSelectField("use_in_product_listing", NULL);
            if ($Data["attrribute_type"] != 'Text Area' and
                    $Data["attrribute_type"] != 'Multiple Select') {
                //Used for Sorting in Product Listing
                $this->checkAndSelectField("use_for_sort_by", NULL);
            }
        }
        if ($Data["attrribute_type"] == 'Text Area') {
            //Enable WYSIWYG
            $this->checkAndSelectField("wysiwyg_enable", NULL);
        }
        if ($Data["attrribute_type"] == 'Text Area' and
                isset($Data["wysiwyg_enable"]) and
                $Data["wysiwyg_enable"] == "No") {
            //Allow HTML Tags on Frontend
            $this->checkAndSelectField("html_allowed_on_front", NULL);
        }
        if ($Data["attrribute_type"] != 'Media Image') {
            //Apply To
            $this->checkAndSelectField("apply_to", NULL);
        }
    }

    /**
     * Fill Manage Label / Options Tab on attribute page
     *
     * @param array $params May contain the following params:
     * attrribute_type,
     */
    public function fillManageLabelAndOptionsTab($params = array())
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attributes/attribute');
        // open Manage Label / Options Tab
        $this->click($this->getUiElement("tabs/manage_label"));
        $this->checkAndFillField("attribute_admin_title", NULL);
        if (isset($Data['store_view_name'])) {
            $qtyStore = $this->getXpathCount($this->getUiElement("elements/store_view_name_for_title"));
            for ($i = 1; $i <= $qtyStore; $i++) {
                $storeName = $this->getText($this->getUiElement("elements/store_view_name_for_title_many", $i));
                if ($storeName == $Data['store_view_name']) {
                    $needStore = $i - 1;
                }
            }
            if ($needStore != NULL) {
                $this->checkAndFillField("attribute_store_view_title", $needStore);
            }
        }
        if ($Data["attrribute_type"] == 'Multiple Select' or $Data["attrribute_type"] == 'Dropdown') {
            for ($i = 0; $i <= count($Data['attribute_admin_option_name']) - 1; $i++) {
                $this->click($this->getUiElement("buttons/add_new_option"));
                if (isset($Data["attribute_admin_option_name"])) {
                    $this->type($this->getUiElement("inputs/attribute_admin_option_name", $i),
                            $Data["attribute_admin_option_name"][$i]);
                }
                if (isset($Data["attribute_admin_option_position"])) {
                    $this->type($this->getUiElement("inputs/attribute_admin_option_position", $i),
                            $Data["attribute_admin_option_position"][$i]);
                }
                //$this->checkAndFillField("attribute_admin_option_name", "$i");
                //$this->checkAndFillField("attribute_admin_option_position", "$i");
                if ($needStore != NULL) {
                    $mas = array($i, $needStore,);
                    if (isset($Data["attribute_store_view_option_name"][$i])) {
                        $this->type($this->getUiElement("inputs/attribute_store_view_option_name", $mas),
                                $Data["attribute_store_view_option_name"][$i]);
                    }
                }
            }
        }
    }

    /**
     * Adds new user attribute
     *
     * @param array $params May contain the following params:
     * attrribute_type,
     */
    public function doCreateAttribute($params = array())
    {
        $Data = $params ? $params : $this->Data;

        // Open Manage Attributes Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/attributes/manage_attibutes"));
        // Click Add new Attribute
        $this->clickAndWait($this->getUiElement("/admin/pages/catalog/attributes/manage_attributes/buttons/add_attribute"));

        $this->fillAttributePropertiesTab($params);

        $this->fillManageLabelAndOptionsTab($params);

        $this->saveAndVerifyForErrors("save_attribute");
    }

    /**
     * Adds new user attribute on product -  need to finish
     *
     * @param array $params May contain the following params:
     * attrribute_type,
     */
    public function doCreateAttributeOnProductPage($params = array())
    {
        $Data = $params ? $params : $this->Data;
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/manageproducts"));
        //$this->setUiNamespace('admin/pages/catalog/categories/manageproducts');
        //$this->clickAndWait($this->getUiElement("buttons/addproduct"));
        //$this->clickAndWait($this->getUiElement("buttons/addproductcontinue"));
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        $this->click($this->getUiElement("buttons/create_new_attribute"));
        $this->waitForPopUp("new_attribute", "30000");
        ;
        $this->selectWindow("name=new_attribute");
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attributes/attribute');
        $this->waitForElement($this->getUiElement('selectors/attrribute_type'), 20);
        $this->fillAttributePropertiesTab();
        $this->fillManageLabelAndOptionsTab();
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attributes/attribute');
        $this->saveAndVerifyForErrors("save_attribute");
        $this->selectWindow("null");
    }

}
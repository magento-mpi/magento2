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
    public function loadConfigData()
    {
        parent::loadConfigData();
        $this->Data = array();
    }

    public function fillAttributePropetriesForm($params)
    {
        $type = $this->isSetValue($params, 'attrribute_type');
        $this->checkAndSelectField($params, 'attrribute_type');
        $this->checkAndFillField($params, 'attrribute_code', NULL);
        if ($type != 'Media Image' and $type != 'Fixed Product Tax') {
            $this->checkAndSelectField($params, 'unique_value');
            $this->checkAndSelectField($params, 'values_required');
        }
        if ($type != 'Price' and $type != 'Fixed Product Tax') {
            $this->checkAndSelectField($params, 'scope');
        }
        $this->checkAndSelectField($params, 'apply_to');
        switch ($type) {
            case 'Text Field':
                $this->checkAndFillField($params, 'default_value_text', NULL);
                $this->checkAndSelectField($params, 'input_validation');
                break;
            case 'Text Area':
                $this->checkAndFillField($params, 'default_value_textarea', NULL);
                break;
            case 'Date':
                $this->checkAndFillField($params, 'default_value_date', NULL);
                break;
            case 'Yes/No':
                $this->checkAndSelectField($params, 'default_value_yesno');
                break;
            case 'Dropdown':
                if ($this->isSetValue($params, 'scope') == 'Global') {
                    $this->checkAndSelectField($params, 'use_to_create_configurable');
                }
                break;
            case 'Price':
                $this->checkAndFillField($params, 'default_value_text', NULL);
                break;
        }
    }

    /**
     * Fill Attribute Properties Tab on attribute page
     *
     * @param array $params May contain the following params:
     * attrribute_type,
     */
    public function fillFrontendPropertiesForm($params)
    {
        $type = $this->isSetValue($params, 'attrribute_type');
        $this->checkAndSelectField($params, 'use_in_quick_search');
        $this->checkAndSelectField($params, 'use_in_advanced_search');
        $this->checkAndSelectField($params, 'comparable');
        $this->checkAndSelectField($params, 'use_for_promo_rules');
        $this->checkAndSelectField($params, 'visible_on_front');
        $this->checkAndSelectField($params, 'use_in_product_listing');
        if ($type != 'Text Area' and $type != 'Multiple Selec') {
            $this->checkAndSelectField($params, 'use_for_sort_by');
        }
        switch ($type) {
            case 'Text Field':
                $this->checkAndSelectField($params, 'html_allowed_on_front');
                break;
            case 'Text Area':
                $this->checkAndSelectField($params, 'wysiwyg_enable');
                if ($this->isSetValue($params, 'wysiwyg_enable') == 'No') {
                    $this->checkAndSelectField($params, 'html_allowed_on_front');
                }
                break;
            case 'Multiple Select': case 'Dropdown': case 'Price':
                $this->checkAndSelectField($params, 'use_in_layered_navigation');
                $this->checkAndSelectField($params, 'use_in_search_results');
                if ($this->isSetValue($params, 'use_in_layered_navigation') != 'No') {
                    $this->checkAndFillField($params, 'position', NULL);
                }
                break;
            default :
                break;
        }
    }

    public function determineStoreNumber($specifiedStoreName)
    {
        $qtyStore = $this->getXpathCount($this->getUiElement('elements/store_view_name_for_title'));
        for ($i = 1; $i <= $qtyStore; $i++) {
            $storeName = $this->getText($this->getUiElement('elements/store_view_name_for_title') . "[$i]");
            if ($storeName == $specifiedStoreName) {
                return $i;
            }
        }
        return -1;
    }

    public function fillTitlesForStore($storeName, $storeTitle)
    {
        $number = $this->determineStoreNumber($storeName);
        if ($number != -1) {
            $this->type($this->getUiElement("inputs/attribute_store_view_title", $number), $storeTitle);
        } else {
            $this->printInfo("store view with name=$storeName does not exist");
        }
    }

    /**
     * Fill Manage Label / Options Tab on attribute page
     *
     * @param array $params May contain the following params:
     * attrribute_type,
     */
    public function fillManageLabelAndOptionsTab($params)
    {
        $Data = $params ? $params : $this->Data;
        $type = $Data['attrribute_type'];
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attributes/attribute');
        // open Manage Label / Options Tab
        $this->click($this->getUiElement('tabs/manage_label'));
        $this->checkAndFillField($params, 'attribute_admin_title', NULL);
        if (isset($Data['attribute_store_view_title'])) {
            foreach ($Data['attribute_store_view_title'] as $key => $value) {
                $this->fillTitlesForStore($key, $value);
            }
        }
        if ($type == 'Multiple Select' or $type == 'Dropdown') {
            $qtyOptions = $this->getXpathCount($this->getUiElement('elements/qty_options'));
            if (isset($Data['attribute_admin_option_name'])) {
                foreach ($Data['attribute_admin_option_name'] as $key => $value) {
                    $i = $key + $qtyOptions;
                    $this->click($this->getUiElement('buttons/add_new_option'));
                    $this->type($this->getUiElement('inputs/attribute_admin_option_name', $i), $value);
                }
            }
            if (isset($Data['attribute_admin_option_position'])) {
                foreach ($Data['attribute_admin_option_position'] as $key => $value) {
                    $i = $key + $qtyOptions;
                    if (!$this->isElementPresent($this->getUiElement('inputs/attribute_admin_option_position', $i))) {
                        $this->click($this->getUiElement('buttons/add_new_option'));
                    }
                    $this->type($this->getUiElement('inputs/attribute_admin_option_position', $i), $value);
                }
            }
            if (isset($Data['attribute_store_view_option_name'])) {
                foreach ($Data['attribute_store_view_option_name'] as $key => $value) {
                    $number = $this->determineStoreNumber($key) - 1;
                    if (is_array($value) and $number != -2) {
                        foreach ($value as $k => $v) {
                            $mas = array($qtyOptions + $k, $number);
                            if (!$this->isElementPresent($this->getUiElement('inputs/attribute_store_view_option_name',
                                                    $mas))) {
                                $this->click($this->getUiElement('buttons/add_new_option'));
                            }
                            $this->type($this->getUiElement('inputs/attribute_store_view_option_name', $mas), $v);
                        }
                    } elseif ($number != -1) {
                        $mas = array($qtyOptions, $number);
                        $this->type($this->getUiElement('inputs/attribute_store_view_option_name', $mas), $v);
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
    public function doCreateAttribute($params)
    {
        $Data = $params ? $params : $this->Data;
        $type = $Data['attrribute_type'];
        // Open Manage Attributes Page
        $this->navigate('Catalog/Attributes/Manage Attributes');
        // Click Add new Attribute
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attributes');
        $this->clickAndWait($this->getUiElement('buttons/add_attribute'));
        // Fill in data
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attributes/attribute');
        $this->fillAttributePropetriesForm($params);
        if ($type != 'Media Image' and $type != 'Fixed Product Tax') {
            $this->fillFrontendPropertiesForm($params);
        }
        $this->fillManageLabelAndOptionsTab($params);
        $this->saveAndVerifyForErrors();
    }

    public function doDeleteAttribute($params)
    {
        // Open Manage Attributes Page
        $this->navigate('Catalog/Attributes/Manage Attributes');
        $this->clickAndWait($this->getUiElement('/admin/global/buttons/reset_search'));
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attributes');
        $searchWord = '/search_product_attribute_/';
        $searchElements = $this->dataPreparation($params, $searchWord);
        $result = $this->searchAndDoAction('attribute_search_grid', $searchElements, 'open', Null);
        if ($result) {
            $this->doDeleteElement();
        }
    }

    /**
     * Adds new user attribute on product -  need to finish
     *
     * @param array $params May contain the following params:
     * attrribute_type,
     */
    public function doCreateAttributeOnProductPage($params)
    {
        $Data = $params ? $params : $this->Data;
        $type = $Data['attrribute_type'];
        $this->navigate('Catalog/Manage Products');
        $this->setUiNamespace('admin/pages/catalog/manage_products');
        $this->clickAndWait($this->getUiElement("buttons/addproduct"));
        $this->setUiNamespace('admin/pages/catalog/manage_products/product');
        $this->checkAndSelectField($params, 'attribute_set');
        $this->setUiNamespace('admin/pages/catalog/manage_products');
        $this->clickAndWait($this->getUiElement("buttons/addproductcontinue"));
        $this->setUiNamespace('admin/pages/catalog/manage_products/product');
        $this->click($this->getUiElement("buttons/create_new_attribute"));
        $this->waitForPopUp("new_attribute", "30000");
        $this->selectWindow("name=new_attribute");
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attributes/attribute');
        $this->waitForElement($this->getUiElement('selectors/attrribute_type'), 20);
        // Fill in data
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attributes/attribute');
        $this->fillAttributePropetriesForm($params);
        if ($type != 'Media Image' and $type != 'Fixed Product Tax') {
            $this->fillFrontendPropertiesForm($params);
        }
        $this->fillManageLabelAndOptionsTab($params);
        $this->click($this->getUiElement("/admin/global/buttons/submit"));
        // check for error message
        if ($this->waitForElement($this->getUiElement('/admin/messages/success'), 20)) {
            $etext = $this->getText($this->getUiElement('/admin/messages/success'));
            $this->printInfo($etext);
        } elseif ($this->waitForElement($this->getUiElement('/admin/messages/error'), 20)) {
            $etext = $this->getText($this->getUiElement('/admin/messages/error'));
            $this->setVerificationErrors($etext);
        } else {
            $this->setVerificationErrors('No success message');
        }
        $this->selectWindow("null");
    }

}
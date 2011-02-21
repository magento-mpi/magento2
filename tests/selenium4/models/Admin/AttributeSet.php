<?php

/**
 * Admin_Scope_Site model
 *
 * @author Magento Inc.
 */
class Model_Admin_AttributeSet extends Model_Admin {

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();
        $this->Data = array();
    }

    /**
     * Adds new attribute set
     *
     * @param array $params  May contain the following params:
     * set_name, based_on
     */
    public function doCreateAtrSet($params)
    {
        // Open Manage Attribute Sets Page
        $this->clickAndWait($this->getUiElement('/admin/topmenu/catalog/attributes/manage_attibute_set'));
        // Add new Attribute Set
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attribute_sets');
        $this->clickAndWait($this->getUiElement('buttons/add_set'));
        // Set UiNamespace
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attribute_sets/attribute_set');
        // Fill fields Name and Based On
        $this->checkAndFillField($params, 'set_name', NULL);
        $result = $this->checkAndSelectField($params, 'based_on');
        // Saving
        if ($result) {
            $this->saveAndVerifyForErrors();
        } else {
            $this->printInfo('Set not created');
        }
    }

    /**
     * Delete attribute set
     *
     * @param array $params
     */
    public function doDeleteAtrSet($params)
    {
        // Open Manage Attribute Sets Page
        $this->clickAndWait($this->getUiElement('/admin/topmenu/catalog/attributes/manage_attibute_set'));
        // Set UiNamespace
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attribute_sets');
        $searchElements = $this->dataPreparation($params, '/search_set/');
        $result = $this->searchAndDoAction('atr_set_search_container', $searchElements, 'open', NULL);
        if ($result) {
            $confirmation =
                    'All products of this set will be deleted! Are you sure you want to delete this attribute set?';
            $deleteResult = $this->doDeleteElement($confirmation);
            if (!$deleteResult) {
                $this->printInfo('Element is not deleleted');
            }
        }
    }

}
<?php

/**
 * Admin customer framework model
 *
 * @author Magento Inc.
 */
class Model_Admin_Tax extends Model_Admin {

    public function loadConfigData()
    {
        parent::loadConfigData();
        $this->taxData = array();
    }

    /**
     * Create Product Tax Class
     *
     * @param string $ProductTaxClassName
     */
    public function doCreateProductTaxClass($params)
    {
        $this->navigate('Sales/Tax/Product Tax Classes');

        $this->setUiNamespace('admin/pages/sales/tax/manage_product_tax_class');
        $this->clickAndWait($this->getUiElement('buttons/add_new'));
        $this->setUiNamespace('admin/pages/sales/tax/manage_product_tax_class/edit_product_tax_class');
        $this->checkAndFillField($params, 'product_tax_class_name', NULL);
        $this->saveAndVerifyForErrors();
    }

    /**
     * Create Customer Tax Class
     *
     * @param string $CustomerTaxClassName
     */
    public function doCreateCustomerTaxClass($params)
    {
        $this->navigate('Sales/Tax/Customer Tax Classes');

        $this->setUiNamespace('admin/pages/sales/tax/manage_customer_tax_class');
        $this->clickAndWait($this->getUiElement('buttons/add_new'));
        $this->setUiNamespace('admin/pages/sales/tax/manage_customer_tax_class/edit_customer_tax_class');
        $this->checkAndFillField($params, 'customer_tax_class_name', NULL);
        $this->saveAndVerifyForErrors();
    }

    /**
     * fill tax rate title for store view
     *
     * @param <type> $storeViewTaxTitles
     */
    public function fillTaxTitles($taxTitles)
    {
        if ($taxTitles != NULL) {
            foreach ($taxTitles as $key => $value) {
                $needStore = NULL;
                $qtyStore = $this->getXpathCount($this->getUiElement("elements/store_view_name_for_tax_title"));
                for ($i = 1; $i <= $qtyStore; $i++) {
                    $storeName = $this->getText($this->getUiElement("elements/store_view_name_for_title_many", $i));
                    if ($storeName == $key) {
                        $needStore = $i;
                    }
                }
                if ($needStore != NULL) {
                    $this->type($this->getUiElement("inputs/store_view_name_for_tax_title", $needStore), $value);
                }
            }
        }
    }

    /**
     * creating Tax Rate
     *
     * @param array $params May contain the following params:
     * tax_rate_identifier, tax_rate_percent, zip_post_code,
     * country, state, store_view_name, store_view_name_for_title_many
     * store_view_name_for_tax_title, tax_store_view_title
     * $qtyStore, $i, $storeName, $needStore
     *
     */
    public function doCreateTaxRate($params)
    {
        $this->navigate('Sales/Tax/Manage Tax Zones & Rates');

        $taxTitles = $this->isSetValue($params, 'tax_titles');
        $this->setUiNamespace('admin/pages/sales/tax/manage_tax_zone_rate');
        $this->clickAndWait($this->getUiElement("buttons/add_new"));
        $this->setUiNamespace('admin/pages/sales/tax/manage_tax_zone_rate/edit_tax_zone_rate');
        // Fill Tax Identifier
        $this->checkAndFillField($params, 'tax_rate_identifier', NULL);
        // Select country
        $this->checkAndSelectField($params, 'country');
        // Select State
        if (!$this->isElementPresent($this->getUiElement('elements/state_disabled'))) {
            $this->checkAndSelectField($params, 'state');
        }
        // Fill Zip/Post is Range and Zip/Post Code
        if ($this->isSetValue($params, 'zip_post_is_range') == 'Yes') {
            $this->checkAndSelectField($params, 'zip_post_is_range');
            $this->checkAndFillField($params, 'zip_range_from', NULL);
            $this->checkAndFillField($params, 'zip_range_to', NULL);
        } elseif ($this->isSetValue($params, 'zip_post_is_range') == 'No') {
            $this->checkAndSelectField($params, 'zip_post_is_range');
            $this->checkAndFillField($params, 'zip_post_code', NULL);
        } else {
            $this->printInfo("Zip is Range selector defined incorect so we use default value No");
            $this->checkAndFillField($params, 'zip_post_code', NULL);
        }
        //Fill Rate Percent
        $this->checkAndFillField($params, 'tax_rate_percent', NULL);
        //fill tax titles
        $this->fillTaxTitles($taxTitles);
        //save tax rate
        $this->saveAndVerifyForErrors();
    }

    /**
     *
     * @param <type> $fieldArray
     */
    public function markOptions($params, $optionName)
    {
        $optionArray = $this->isSetValue($params, $optionName);
        if ($optionArray != NULL) {
            foreach ($optionArray as $value) {
                if ($this->isElementPresent($this->getUiElement('inputs/' . $optionName) .
                                "/option[text()='" . $value . "']")) {
                    $this->addSelection($this->getUiElement('inputs/' . $optionName), 'label=' . $value);
                } else {
                    $this->printInfo("The value '" . $value . "' cannot be set for the field '" . $optionName . "'");
                }
            }
        }
    }

    /**
     * Create Tax Rule
     *
     * @param array $params May contain the following params:
     * tax_rule_name, customer_tax_class_name, product_tax_class_name,
     * tax_rate_identifier, tax_rule_priority, tax_rule_sort_order
     *
     */
    public function doCreateTaxRule($params)
    {
        $this->navigate('Sales/Tax/Manage Tax Rules');

        $this->setUiNamespace('admin/pages/sales/tax/manage_tax_rule');
        $this->clickAndWait($this->getUiElement("buttons/add_new_tax_rule"));
        $this->setUiNamespace('admin/pages/sales/tax/manage_tax_rule/edit_tax_rule');
        // Fill Name
        $this->checkAndFillField($params, 'tax_rule_name', NULL);
        //Select Customer Tax Class
        $this->markOptions($params, 'customer_tax_class');
        // Select Product Tax Class
        $this->markOptions($params, 'product_tax_class');
        // Select Tax Rate 
        $this->markOptions($params, 'tax_rate');
        // Fill Priority and Sort Order
        $this->checkAndFillField($params, 'tax_rule_priority', NULL);
        $this->checkAndFillField($params, 'tax_rule_sort_order', NULL);
        //save tax rule
        $this->saveAndVerifyForErrors();
    }

    /**
     * Delete Tax Element
     *
     * @param <type> $params
     * @param <type> $element
     */
    public function doDeleteTaxElement($params, $element)
    {
        switch ($element) {
            case 'rule':
                $searchWord = '/^search_tax_rule_/';
                $tableContainer = 'tax_rule_container';
                $this->setUiNamespace('admin/pages/sales/tax/manage_tax_rule');
                $this->navigate('Sales/Tax/Manage Tax Rules');
                break;
            case 'rate':
                $searchWord = '/^search_tax_rate_/';
                $tableContainer = 'tax_rate_container';
                $this->setUiNamespace('admin/pages/sales/tax/manage_tax_zone_rate');
                $this->navigate('Sales/Tax/Manage Tax Zones & Rates');
                break;
            case 'productClass':
                $searchWord = '/^search_product_tax_class_/';
                $this->setUiNamespace('admin/pages/sales/tax/manage_product_tax_class');
                $tableContainer = 'product_class_container';
                $this->navigate('Sales/Tax/Product Tax Classes');
                break;
            case 'customerClass':
                $searchWord = '/^search_customer_tax_class_/';
                $tableContainer = 'customer_class_container';
                $this->setUiNamespace('admin/pages/sales/tax/manage_customer_tax_class');
                $this->navigate('Sales/Tax/Customer Tax Classes');
                break;
            default:
                break;
        }
        $searchElements = $this->dataPreparation($params, $searchWord);
        $search_res = $this->searchAndDoAction($tableContainer, $searchElements, 'open', NULL);
        if ($search_res) {
            $deleteResult = $this->doDeleteElement();
            if (!$deleteResult) {
                $this->printInfo('Element is not deleleted');
            }
        }
    }

}
<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAttribute
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ProductAttribute_Helper extends Mage_Selenium_AbstractHelper
{
    #*********************************************************************************
    #*                      Creation attribute helper methods                        *
    #*********************************************************************************

    /**
     * Action_helper method for Create Attribute
     * Preconditions: 'Manage Attributes' page is opened.
     *
     * @param array $attributeData Array which contains DataSet for filling of the current form
     */
    public function createAttribute($attributeData)
    {
        $this->clickButton('add_new_attribute');
        $this->fillAttributeTabs($attributeData);
        $this->saveForm('save_attribute');
    }

    /**
     * Edit product attribute
     *
     * @param string $attributeCode
     * @param array $editedData
     */
    public function editAttribute($attributeCode, array $editedData)
    {
        $this->openAttribute(array('attribute_code' => $attributeCode));
        $this->fillAttributeTabs($editedData);
        $this->saveForm('save_attribute');
    }

    /**
     * Create Attribute from product page.
     * Preconditions: Product page is opened.
     *
     * @param array $attrData
     * @param string $saveInAttributeSet
     */
    public function createAttributeOnProductTab($attrData, $saveInAttributeSet = '')
    {
        //Steps Click 'Create New Attribute' button.
        $saveButton = $saveInAttributeSet ? 'save_in_new_attribute_set' : 'save_attribute' ;
        $currentPage = $this->getCurrentPage();
        $this->clickButton('create_new_attribute', false);
        $this->waitForControl(self::FIELD_TYPE_PAGEELEMENT, 'add_new_attribute_iframe');
        $this->pleaseWait();
        $this->frame('create_new_attribute_container');
        $this->setCurrentPage('new_product_attribute_from_product_page');
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'attribute_properties');

        //Dirty hack (waiting for UI redesign MAGETWO-10046)
        $this->clickControl(self::FIELD_TYPE_PAGEELEMENT, 'frontend_properties_section', false);
        $this->clickControl(self::FIELD_TYPE_PAGEELEMENT, 'manage_titles_section', false);
        $this->clickControl(self::FIELD_TYPE_PAGEELEMENT, 'advanced_attribute_properties_section', false);

        $this->fillForm($attrData);
        $this->fillManageOptions($attrData);
        if (isset($attrData['store_view_titles'])) {
            $this->storeViewTitles($attrData);
        }
        if (isset($attrData['frontend_properties'])) {
            $this->fillForm($attrData['frontend_properties']);
        }

        $waitCondition = $this->getBasicXpathMessagesExcludeCurrent(array('error', 'validation'));
        if (isset($attrData['attribute_code'])) {
            $this->addParameter('elementId', 'attribute-' . $attrData['attribute_code'] . '-container');
            $waitCondition[] = $this->_getControlXpath('pageelement', 'element_by_id');
        }
        $this->clickButton($saveButton, false);
        if ($saveInAttributeSet) {
            $this->alertText($saveInAttributeSet);
            $this->acceptAlert();
        }
        $this->waitForElementVisible($waitCondition);
        $this->frame(null);
        $this->setCurrentPage($currentPage);
    }

    /**
     * Open Product Attribute.
     * Preconditions: 'Manage Attributes' page is opened.
     *
     * @param array $searchData
     */
    public function openAttribute($searchData)
    {
        $this->waitForControlVisible('fieldset', 'attributes_grid');
        $searchData = $this->_prepareDataForSearch($searchData);
        $xpathTR = $this->search($searchData, 'attributes_grid');
        $this->assertNotNull($xpathTR, 'Attribute is not found');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $this->getColumnIdByName('Attribute Code'));
        $text = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
        $this->addParameter('elementTitle', $text);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->clickControl('pageelement', 'table_line_cell_index');
    }

    #*********************************************************************************
    #*                         Fill in attribute helper methods                      *
    #*********************************************************************************
    /**
     * Fill in attribute data
     *
     * @param array $attributeData
     */
    public function fillAttributeTabs(array $attributeData)
    {
        if (!$this->isControlExpanded(self::UIMAP_TYPE_FIELDSET, 'advanced_attribute_properties')) {
            $this->clickControl(self::UIMAP_TYPE_FIELDSET, 'advanced_attribute_properties', false);
        }
        $this->fillTab($attributeData, 'properties', false);
        $this->fillManageOptions($attributeData);
        if (isset($attributeData['store_view_titles'])) {
            $this->openTab('manage_labels_options');
            $this->storeViewTitles($attributeData);
        }
        if (isset($attributeData['frontend_properties'])) {
            $this->openTab('frontend_properties');
            $this->fillTab($attributeData['frontend_properties'], 'frontend_properties', false);
        }
    }

    /**
     * Fill Manage options for Dropdown and Multiple Select Attributes
     *
     * @param $attribute
     */
    public function fillManageOptions($attribute)
    {
        $options = preg_grep('/^option_\d+$/', array_keys($attribute));
        if (empty($options)) {
            return;
        }
        $this->assertTrue($this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'manage_options'));
        $optionCount = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'manage_options_option');
        $optionOrder = array();
        foreach ($options as $option) {
            if (!is_array($attribute[$option])) {
                $this->fail('Invalid data is provided for filling attribute options.');
            }
            $this->clickButton('add_option', false);
            $this->addParameter('fieldOptionNumber', $optionCount);
            $this->waitForControlEditable(self::FIELD_TYPE_INPUT, 'admin_option_name');
            if (isset($attribute[$option]['option_position'])) {
                $optionOrder[$optionCount] = $attribute[$option]['option_position'];
                unset($attribute[$option]['option_position']);
            } else {
                $optionOrder[$optionCount] = 'noValue';
            }
            $this->storeViewTitles($attribute[$option], 'manage_options');
            $this->fillFieldset($attribute[$option], 'manage_options');
            $optionCount = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'manage_options_option');
        }
        $this->orderBlocks($optionOrder, 'fieldOptionNumber', 'move_attribute_option_row', 'option_orders');
    }

    /**
     * Fill or Verify Titles for different Store View
     *
     * @param array $attrData
     * @param string $fieldsetName
     * @param string $action
     */
    public function storeViewTitles($attrData, $fieldsetName = 'manage_titles', $action = 'fill')
    {
        $name = 'store_view_titles';
        $columnShift = $fieldsetName == 'manage_options' ? 2 : 0;
        if (array_key_exists($name, $attrData) && is_array($attrData[$name])) {
            $this->addParameter('tableHeadXpath', $this->_getControlXpath('fieldset', $fieldsetName) . '//thead');
            $qtyStore = $this->getControlCount('pageelement', 'table_column');
            foreach ($attrData[$name] as $storeViewName => $storeViewValue) {
                $number = -1;
                for ($i = 1; $i <= $qtyStore; $i++) {
                    $this->addParameter('index', $i);
                    if ($this->getControlAttribute('pageelement', 'table_column_index', 'text') == $storeViewName) {
                        $number = $i;
                        break;
                    }
                }
                if ($number != -1) {
                    $number -= $columnShift;
                    $this->addParameter('storeViewNumber', $number);
                    $fieldName = preg_replace('/^manage_/', '', $fieldsetName) . '_by_store_name';
                    switch ($action) {
                        case 'fill':
                            $this->fillField($fieldName, $storeViewValue);
                            break;
                        case 'verify':
                            $actualText = $this->getControlAttribute('field', $fieldName, 'value');
                            $var = array_flip(get_html_translation_table());
                            $actualText = strtr($actualText, $var);
                            $this->assertEquals($storeViewValue, $actualText, 'Stored data not equals to specified');
                            break;
                    }
                } else {
                    $this->fail('Cannot find specified Store View with name \'' . $storeViewName . '\'');
                }
            }
        }
    }

    #*********************************************************************************
    #*                    Verification attribute helper methods                      *
    #*********************************************************************************
    /**
     * Verify all data in saved Attribute.
     * Preconditions: Attribute page is opened.
     *
     * @param array $attrData
     */
    public function verifyAttribute($attrData)
    {
        $this->assertTrue($this->verifyForm($attrData, 'properties'), $this->getParsedMessages());
        if (!$this->isControlExpanded(self::UIMAP_TYPE_FIELDSET, 'advanced_attribute_properties')) {
            $this->clickControl(self::UIMAP_TYPE_FIELDSET, 'advanced_attribute_properties', false);
        }
        $this->verifyForm($attrData, 'properties');
        $this->verifyManageOptions($attrData);
        if (isset($attrData['store_view_titles'])) {
            $this->openTab('manage_labels_options');
            $this->storeViewTitles($attrData, 'manage_titles', 'verify');
        }
        if (isset($attrData['frontend_properties'])) {
            $this->openTab('frontend_properties');
            $this->verifyForm($attrData['frontend_properties'], 'frontend_properties');
        }
    }

    /**
     * Verify Manage options for Dropdown and Multiple Select Attributes
     *
     * @param array $attribute
     */
    public function verifyManageOptions($attribute)
    {
        $options = preg_grep('/^option_\d+$/', array_keys($attribute));
        if (empty($options)) {
            return;
        }
        $this->assertTrue($this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'manage_options'));
        $optionCount = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'manage_options_option');
        $optionOrder = array();
        foreach ($options as $option) {
            $optionOrder[$attribute[$option]['admin_option_name']] = isset($attribute[$option]['option_position'])
                ? $attribute[$option]['option_position']
                : 'noValue';
        }
        $this->verifyBlocksOrder($optionOrder, 'option_orders');
        $itemDataOrder = $this->getActualItemOrder(self::FIELD_TYPE_INPUT, 'option_orders');
        foreach ($options as $option) {
            if (!is_array($attribute[$option])) {
                $this->fail('Invalid data is provided for filling attribute options.');
            }
            if ($optionCount-- > 0) {
                $this->addParameter('index', $itemDataOrder[$attribute[$option]['admin_option_name']]);
                $optionNumber = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'is_default_option_index',
                    'selectedValue');
                $this->addParameter('fieldOptionNumber', $optionNumber);
                $this->assertTrue($this->verifyForm($attribute[$option]), $this->getParsedMessages());
                $this->storeViewTitles($attribute[$option], 'manage_options', 'verify');
            }
        }
    }

    /**
     * Verify whether product has custom option
     *
     * @param $key
     * @param $value
     * @param $option
     *
     * @return bool
     */
    protected function _hasOptions($key, $value, $option)
    {
        return preg_match('/^option_/', $key) && is_array($value)
               && $this->controlIsPresent('fieldset', 'manage_options')
               && $option > 0;
    }

    /**
     * Verify dropdown system attribute on Manage Options tab:
     * Manage Titles is present, Manage Options are present and disabled,
     * Delete and Add Option buttons are absent
     *
     * @param array $attributeData
     */
    public function verifySystemAttribute($attributeData)
    {
        $this->openTab('properties');
        $setDeFaultValue = isset($attributeData['default_value']);
        $dataWithoutOptions = $this->processAttributeValue($attributeData, true, $setDeFaultValue);
        $this->storeViewTitles($dataWithoutOptions, 'manage_titles', 'verify');
        if ($this->buttonIsPresent('add_option')) {
            $this->addVerificationMessage('"Add Option" button is present');
        }
        if ($this->buttonIsPresent('delete_option')) {
            $this->addVerificationMessage('"Delete" button is present in Manage Options tab');
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Set default value for dropdown attribute and verify admin values if $isCheck = true
     *
     * @param array $attributeData
     * @param bool $isCheck
     * @param bool $setDefaultValue
     *
     * @return array
     */
    public function processAttributeValue(array $attributeData, $isCheck = false, $setDefaultValue = false)
    {
        $options = array();
        $isSetDefault = false;
        $this->openTab('properties');
        $optionLines = $this->getControlElements(self::FIELD_TYPE_PAGEELEMENT, 'manage_options_option');
        $optionCount = count($optionLines);
        $identificator = 0;
        foreach ($attributeData as $key => $value) {
            if ($this->_hasOptions($key, $value, $optionCount)) {
                $options[$identificator++] = $value;
                $optionCount--;
                unset($attributeData[$key]);
            }
        }
        $locator = "//input[@class='input-text required-option' and @disabled='disabled']";
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $optionLine
         */
        foreach ($optionLines as $key => $optionLine) {
            $admin = $this->getChildElement($optionLine, $locator);
            $currentValue = trim($admin->value());
            $this->addParameter('rowNumber', $key + 1);
            if ($isCheck) {
                if (!isset($options[$key]) || !isset($options[$key]['admin_option_name'])) {
                    $this->addVerificationMessage('Admin Option Name for option with index ' . $key
                        . ' is not set. Exist more options than specified.');
                    continue;
                }
                $expectedValue = $options[$key]['admin_option_name'];
                if ($this->controlIsPresent('field', 'admin_option_name_disabled')) {
                    if ($expectedValue != $currentValue) {
                        $this->addVerificationMessage(
                            "Admin value attribute label is wrong.\nExpected: " . $options[$key]['admin_option_name']
                            . "\nActual: " . $currentValue);
                    }
                } else {
                    $this->addVerificationMessage('Admin value attribute in ' . $key . ' row is not disabled');
                }
            }
            if ($setDefaultValue && isset($attributeData['default_value'])
                && $attributeData['default_value'] == $currentValue
            ) {
                $this->addParameter('optionName', $currentValue);
                $this->fillCheckbox('default_value_by_option_name', 'Yes');
                $isSetDefault = true;
                $setDefaultValue = false;
            }
        }
        if ($isSetDefault == false && $setDefaultValue) {
            $this->addVerificationMessage('Default option can not be set as it does not exist');
        }
        $this->assertEmptyVerificationErrors();
        return $attributeData;
    }
}

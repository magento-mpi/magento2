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
    /**
     * Action_helper method for Create Attribute
     * Preconditions: 'Manage Attributes' page is opened.
     *
     * @param array $attrData Array which contains DataSet for filling of the current form
     */
    public function createAttribute($attrData)
    {
        $this->clickButton('add_new_attribute');
        $this->fillTab($attrData, 'properties', false);
        if (!$this->fillTab($attrData, 'manage_labels_options', false)) {
            $this->openTab('manage_labels_options');
        }
        $this->storeViewTitles($attrData);
        $this->attributeOptions($attrData);
        $this->saveForm('save_attribute');
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

    /**
     * Verify all data in saved Attribute.
     * Preconditions: Attribute page is opened.
     *
     * @param array $attrData
     */
    public function verifyAttribute($attrData)
    {
        $this->assertTrue($this->verifyForm($attrData, 'properties'), $this->getParsedMessages());
        $this->openTab('manage_labels_options');
        $this->storeViewTitles($attrData, 'manage_titles', 'verify');
        $this->attributeOptions($attrData, 'verify');
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
        $this->setCurrentPage('new_product_attribute');
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'attribute_properties');
        $this->fillTab($attrData, 'properties', false);
        if (!$this->fillTab($attrData, 'manage_labels_options', false)) {
            $this->openTab('manage_labels_options');
        }
        $this->storeViewTitles($attrData);
        $this->attributeOptions($attrData);
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
     * Fill or Verify Titles for different Store View
     *
     * @param array $attrData
     * @param string $fieldsetName
     * @param string $action
     */
    public function storeViewTitles($attrData, $fieldsetName = 'manage_titles', $action = 'fill')
    {
        $name = 'store_view_titles';
        $columnShift = $fieldsetName == 'manage_options' ? 1 : 0;
        if (isset($attrData['admin_title'])) {
            $attrData[$name]['Admin'] = $attrData['admin_title'];
        }
        if (array_key_exists($name, $attrData) && is_array($attrData[$name])) {
            $this->addParameter('tableHeadXpath', $this->_getControlXpath('fieldset', $fieldsetName));
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

    /**
     * Fill or Verify Options for Dropdown and Multiple Select Attributes
     *
     * @param array $attrData
     * @param string $action
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function attributeOptions($attrData, $action = 'fill')
    {
        $optionCount = $this->getControlCount('pageelement', 'manage_options_option');
        $number = 1;
        foreach ($attrData as $fKey => $dValue) {
            if (preg_match('/^option_/', $fKey) and is_array($attrData[$fKey])) {
                if ($this->controlIsPresent('fieldset', 'manage_options')) {
                    switch ($action) {
                        case 'fill':
                            $this->addParameter('fieldOptionNumber', $optionCount);
                            $this->clickButton('add_option', false);
                            $this->waitForControlEditable('field', 'option_position');
                            $this->storeViewTitles($attrData[$fKey], 'manage_options');
                            $this->fillFieldset($attrData[$fKey], 'manage_options');
                            $optionCount = $this->getControlCount('pageelement', 'manage_options_option');
                            break;
                        case 'verify':
                            if ($optionCount-- > 0) {
                                $this->addParameter('index', $number++);
                                $optionNumber = $this->getControlAttribute('pageelement', 'is_default_option_index',
                                    'selectedValue');
                                $this->addParameter('fieldOptionNumber', $optionNumber);
                                $this->assertTrue($this->verifyForm($attrData[$fKey], 'manage_labels_options'),
                                    $this->getParsedMessages());
                                $this->storeViewTitles($attrData[$fKey], 'manage_options', 'verify');
                            }
                            break;
                    }
                }
            }
        }
    }

    /**
     * Define Attribute Id
     *
     * @param array $searchData
     *
     * @return int
     */
    public function defineAttributeId(array $searchData)
    {
        $this->navigate('manage_attributes');
        $attrXpath = $this->search($searchData, 'attributes_grid');
        $this->assertNotEquals(null, $attrXpath);

        return $this->defineIdFromTitle($attrXpath);
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
        $this->openTab('manage_labels_options');
        $optionLines = $this->getControlElements('pageelement', 'option_line');
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
                    $this->addVerificationMessage('Admin Option Name for option with index ' . $key + 1
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
        $this->openTab('manage_labels_options');
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
     * Edit product attribute
     *
     * @param string $attributeCode
     * @param array $editedData
     */
    public function editAttribute($attributeCode, array $editedData)
    {
        $this->openAttribute(array('attribute_code' => $attributeCode));
        $this->fillTab($editedData, 'properties', false);
        if (!$this->fillTab($editedData, 'manage_labels_options', false)) {
            $this->openTab('manage_labels_options');
        }
        $this->storeViewTitles($editedData);
        $this->attributeOptions($editedData);
        $this->saveForm('save_attribute');
    }
}

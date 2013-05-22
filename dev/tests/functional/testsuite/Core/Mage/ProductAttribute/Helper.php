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
        $searchData = $this->_prepareDataForSearch($searchData);
        $xpathTR = $this->search($searchData, 'attributes_grid');
        $this->assertNotNull($xpathTR, 'Attribute is not found');
        $attributeRowElement = $this->getElement($xpathTR);
        $attributeUrl = $attributeRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Attribute Code');
        $cellElement = $this->getChildElement($attributeRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineParameterFromUrl('attribute_id', $attributeUrl));
        //Open attribute
        $this->url($attributeUrl);
        $this->validatePage('edit_product_attribute');
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
        $saveButton = $saveInAttributeSet ? 'save_in_new_attribute_set' : 'save_attribute';
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
     *
     * @return array
     */
    public function processAttributeValue(array $attributeData, $isCheck = true)
    {
        $options = array();
        $this->openTab('manage_labels_options');
        if ($isCheck) {
            $optionCount = $this->getControlCount('pageelement', 'option_line');
            $identifier = 0;
            foreach ($attributeData as $key => $value) {
                if ($this->_hasOptions($key, $value, $optionCount)) {
                    $options[$identifier++] = $value;
                    $optionCount--;
                    unset($attributeData[$key]);
                }
            }
            $locator = "//input[@class='input-text required-option' and @disabled='disabled']";
            /** @var PHPUnit_Extensions_Selenium2TestCase_Element $optionLine */
            foreach ($this->getControlElements('pageelement', 'option_line') as $key => $optionLine) {
                $admin = $this->getChildElement($optionLine, $locator);
                $currentValue = trim($admin->value());
                if (!isset($options[$key]) || !isset($options[$key]['admin_option_name'])) {
                    $this->addVerificationMessage('Admin Option Name for option with index ' . ($key + 1)
                        . ' is not set. Exist more options than specified.');
                    continue;
                }
                if ($options[$key]['admin_option_name'] != $currentValue) {
                    $this->addVerificationMessage('Admin value attribute label is wrong. Expected="'
                        . $options[$key]['admin_option_name'] . '" Actual="' . $currentValue . '"');
                }
            }
            if (isset($attributeData['default_value'])) {
                $this->addParameter('optionName', $attributeData['default_value']);
                if (!$this->getControlAttribute('checkbox', 'default_value_by_option_name', 'selectedValue')) {
                    $this->addVerificationMessage($attributeData['default_value'] . ' is not set as default value');
                }
            }
        }
        if (isset($attributeData['set_default_value'])) {
            $this->addParameter('optionName', $attributeData['set_default_value']);
            $this->fillCheckbox('default_value_by_option_name', 'Yes');
        }
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
        $withoutOptions = $this->processAttributeValue($attributeData);
        $this->storeViewTitles($withoutOptions, 'manage_titles', 'verify');
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

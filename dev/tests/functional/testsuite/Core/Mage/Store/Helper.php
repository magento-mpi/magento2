<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store
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
class Core_Mage_Store_Helper extends Mage_Selenium_TestCase
{
    /**
     * Create Website|Store|Store View
     *
     * Preconditions: 'Manage Stores' page is opened.
     *
     * @param array|string $data
     * @param string $name
     */
    public function createStore($data, $name)
    {
        if (is_string($data)) {
            $elements = explode('/', $data);
            $fileName = (count($elements) > 1)
                ? array_shift($elements)
                : '';
            $data = $this->loadDataSet($fileName, implode('/', $elements));
        }

        $this->clickButton('create_' . $name);
        $this->fillForm($data);
        $this->saveForm('save_' . $name);
    }

    /**
     * Delete Website|Store|Store View
     *
     * @param array $storeData
     *
     * @return boolean
     */
    public function deleteStore(array $storeData)
    {
        //Determination of element name
        $elementName = '';
        foreach ($storeData as $fieldName => $fieldValue) {
            if (preg_match('/_name$/', $fieldName)) {
                $elementName = $fieldName;
            }
        }
        $element = preg_replace('/_name$/', '', $elementName);
        if ($elementName == '') {
            $this->fail('It is impossible to determine what needs to be deleted');
        }
        //Search
        $this->clickButton('reset_filter');
        $this->fillField($elementName, $storeData[$elementName]);
        $this->clickButton('search');
        //Determination of found items amount
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'manage_stores');
        $qtyElementsInTable = $this->_getControlXpath('pageelement', 'qtyElementsInTable');
        $foundItems = $this->getText($fieldsetXpath . $qtyElementsInTable);
        if ($foundItems == 0) {
            $this->fail('No records found.');
        }
        //Determination of row id
        $names = $this->getTableHeadRowNames();
        foreach ($names as $key => $value) {
            $names[$key] = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '_', $value)), '_');
        }
        $number = (in_array($elementName, $names))
            ? array_search($elementName, $names) + 1
            : 0;
        //Deletion
        $error = false;
        $this->addParameter('elementTitle', $storeData[$elementName]);
        for ($i = 1; $i <= $foundItems; $i++) {
            //Definition element url
            $xpath = $fieldsetXpath . '//table[@id]/tbody' . '/tr[' . $i . ']/td[' . $number . ']/a';
            $url = $this->getAttribute($xpath . '@href');
            //Open element
            $this->addParameter('id', $this->defineIdFromUrl($url));
            $this->openWindow($url, 'edit');
            $this->selectWindow('name=edit');
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            $this->validatePage('edit_' . $element);
            //Searching a necessary element
            if ($this->verifyForm($storeData)) {
                if ($this->controlIsPresent('button', 'delete_' . $element)) {
                    $this->clickButton('delete_' . $element);
                    $this->fillDropdown('create_backup', 'No');
                    $this->clickButton('delete_' . $element);
                    $this->assertMessagePresent('success', 'success_deleted_' . $element);
                    $this->close();
                    $this->selectWindow(null);

                    return true;
                } else {
                    $error = true;
                    $this->close();
                    $this->selectWindow(null);
                }
            } else {
                $this->close();
                $this->selectWindow(null);
            }
        }

        if ($error) {
            $this->fail('It is impossible to delete ' . $element);
        }

        return false;
    }

    /**
     * Selects a store view from 'Choose Store View' drop-down in backend
     *
     * @param string $controlName Name of the dropdown from UIMaps
     * @param string $website Default = 'Main Website'
     * @param string $store Default = 'Main Website Store'
     * @param string $storeView Default = 'Default Store View'
     *
     * @throws PHPUnit_Framework_Exception
     */
    public function selectStoreView($controlName, $website = 'Main Website', $store = 'Main Website Store', $storeView = 'Default Store View')
    {
        $fieldXpath = $this->_getControlXpath('dropdown', $controlName);
        $storeViewXpath = $fieldXpath . "/optgroup[normalize-space(@label) = '$website']"
                          . "/following-sibling::optgroup[contains(@label,'$store')][1]"
                          . "/option[contains(text(),'$storeView')]";
        if (!$this->isElementPresent($storeViewXpath)) {
            throw new PHPUnit_Framework_Exception('Cannot find option ' . $storeViewXpath);
        }
        $optionValue = $this->getValue($storeViewXpath);
        //Try to select by value first, since there may be options with equal labels.
        if (isset($optionValue)) {
            $this->select($fieldXpath, 'value=' . $optionValue);
        } else {
            $this->select($fieldXpath, 'label=' . 'regexp:^\s+' . preg_quote($storeView));
        }
        $this->getConfirmation();
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
    }
}

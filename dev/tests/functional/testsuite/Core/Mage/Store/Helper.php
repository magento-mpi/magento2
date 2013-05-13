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
class Core_Mage_Store_Helper extends Mage_Selenium_AbstractHelper
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
        $data = $this->fixtureDataToArray($data);

        $this->clickButton('create_' . $name);
        $this->fillForm($data);
        $this->saveForm('save_' . $name);
    }

    /**
     * Determination of element name
     *
     * @param array $storeData
     * @return string $elementName
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _determineElementName($storeData)
    {
        $elementName = '';
        foreach ($storeData as $fieldName => $fieldValue) {
            if (preg_match('/_name$/', $fieldName)) {
                $elementName = $fieldName;
            }
        }
        return $elementName;
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
        $elementName = $this->_determineElementName($storeData);
        $element = preg_replace('/_name$/', '', $elementName);
        if ($elementName == '') {
            $this->fail('It is impossible to determine what needs to be deleted');
        }
        //Search
        $this->clickButton('reset_filter');
        $this->fillField($elementName, $storeData[$elementName]);
        $this->clickButton('search');
        //Determination of found items amount
        $fieldsetLocator = $this->_getControlXpath('fieldset', 'manage_stores');
        list(, , $foundItems) = explode('|', $this->getElement($fieldsetLocator . "//div[@class='pager']")->text());
        $foundItems = trim(preg_replace('/[A-Za-z]+/', '', $foundItems));
        if ($foundItems == 0) {
            $this->fail('No records found.');
        }
        //Determination of row id
        $names = $this->getTableHeadRowNames();
        foreach ($names as $key => $value) {
            $names[$key] = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '_', $value)), '_');
        }
        $number = (in_array($elementName, $names)) ? array_search($elementName, $names) + 1 : 0;
        //Deletion
        $error = false;
        $this->addParameter('elementTitle', $storeData[$elementName]);
        for ($i = 1; $i <= $foundItems; $i++) {
            //Definition element url
            $this->addParameter('rowIndex', $i);
            $this->addParameter('cellIndex', $number);
            $url = $this->getControlAttribute('pageelement', 'cell_store_link', 'href');
            //Open element
            $this->addParameter('id', $this->defineIdFromUrl($url));
            $this->execute(array('script' => "window.open()", 'args' => array()));
            $windows = $this->windowHandles();
            $this->window(end($windows));
            $this->url($url);
            $this->validatePage('edit_' . $element);
            //Searching a necessary element
            if ($this->verifyForm($storeData)) {
                if ($this->controlIsPresent('button', 'delete_' . $element)) {
                    $this->clickButton('delete_' . $element);
                    $this->fillDropdown('create_backup', 'No');
                    $this->clickButton('delete_' . $element);
                    $this->assertMessagePresent('success', 'success_deleted_' . $element);
                    $this->closeWindow();
                    $this->window('');

                    return true;
                } else {
                    $error = true;
                    $this->closeWindow();
                    $this->window('');
                }
            } else {
                $this->closeWindow();
                $this->window('');
            }
        }

        if ($error) {
            $this->fail('It is impossible to delete ' . $element);
        }

        return false;
    }

    /**
     * Create Status Order
     * Preconditions: 'New Order Status' page is opened.
     *
     * @param array|string $data
     */
    public function createStatus($data)
    {
        $data = $this->fixtureDataToArray($data);

        $this->clickButton('create_new_status');
        $this->fillFieldSet($data, 'order_status_info');
        $this->saveForm('save_status');
    }

    /**
     * Assign Order Status new state values
     * Preconditions: 'Order statuses' page is opened.
     *
     * @param array|string $data
     */
    public function assignStatus($data)
    {
        $data = $this->fixtureDataToArray($data);
        $this->clickButton('assign_status_to_state');
        $this->fillFieldSet($data, 'assignment_information');
        $this->saveForm('save_status_assignment');
    }

    /**
     * Delete all Store Views except specified in $excludeList
     *
     * @param array $excludeList
     * @return bool
     */
    public function deleteStoreViewsExceptSpecified(array $excludeList = array('Default Store View'))
    {
        return $this->deleteStoresByType('store_view', $excludeList);
    }

    /**
     * Delete all Store Views|Store|Website except specified in $excludeList
     *
     * @param string $type store|store_view|website
     * @param array $exclude
     * @return bool
     */
    public function deleteStoresByType($type, array $exclude = array())
    {
        $id = $this->getColumnIdByName(ucwords(str_replace('_', ' ', $type)) . ' Name');
        $this->addParameter('tableHeadXpath', $this->_getControlXpath('pageelement', 'stores_table'));
        $toDelete = array();
        do {
            $isNextPage = $this->controlIsVisible('link', 'next_page');
            /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
            foreach ($this->getControlElements('pageelement', 'table_line') as $element) {
                $name = trim($this->getChildElement($element, 'td[' . $id . ']')->text());
                if ($name !== '' && !in_array($name, $exclude)) {
                    $url = $this->getChildElement($element, 'td[' . $id . ']/a')->attribute('href');
                    $toDelete[$url] = $name;
                }
            }
            if ($isNextPage) {
                $this->clickControl('link', 'next_page', false);
                $this->waitForPageToLoad();
            }
        } while ($isNextPage);
        foreach ($toDelete as $url => $name) {
            $this->url($url);
            $this->addParameter('elementTitle', $name);
            $this->addParameter('id', $this->defineIdFromUrl($url));
            $this->validatePage();
            if ($this->controlIsVisible('button', 'delete_' . $type)) {
                $this->clickButton('delete_' . $type);
                $this->fillDropdown('create_backup', 'No');
                $this->clickButton('delete_' . $type);
                $this->assertMessagePresent('success', 'success_deleted_' . $type);
                unset($toDelete[$url]);
            } else {
                $this->navigate('manage_stores');
            }
        }
        return $toDelete;
    }

    /**
     * @param array $excludeWebsite
     * @param array $excludeStore
     * @param array $excludeStoreView
     */
    public function deleteAllStoresExceptSpecified(
        $excludeWebsite = array('Main Website'),
        $excludeStore = array('Main Website Store'),
        $excludeStoreView = array('Default Store View')
    )
    {
        $this->deleteStoresByType('website', $excludeWebsite);
        $this->deleteStoresByType('store_view', $excludeStoreView);
        $this->deleteStoresByType('store', $excludeStore);
    }

}
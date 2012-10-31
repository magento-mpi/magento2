<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Status
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
class Community2_Mage_Store_Helper extends Core_Mage_Store_Helper
{
    /**
     * Create Status Order
     * Preconditions: 'New Order Status' page is opened.
     *
     * @param array|string $data
     */
    public function createStatus($data)
    {
        if (is_string($data)) {
            $elements = explode('/', $data);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $data = $this->loadDataSet($fileName, implode('/', $elements));
        }

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
        if (is_string($data)) {
            $elements = explode('/', $data);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $data = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $this->clickButton('assign_status_to_state');
        $this->fillFieldSet($data, 'assignment_information');
        $this->saveForm('save_status_assignment');
    }

    /**
     * Delete all Store Views except specified in $excludeList
     *
     * @param array $excludeList
     */
    public function deleteStoreViewsExceptSpecified(array $excludeList = array('Default Store View'))
    {
        $excludeList[] = '';
        $fieldsetLocator = $this->_getControlXpath('fieldset', 'manage_stores');
        list(, , $totalCount) = explode('|', $this->getElement($fieldsetLocator . "//td[@class='pager']")->text());
        $totalCount = trim(preg_replace('/[A-Za-z]+/', '', $totalCount));
        if ($totalCount > 20) {
            $this->addParameter('limit', 200);
            $this->fillDropdown('items_per_page', 200);
            $this->waitForPageToLoad();
            $this->validatePage('manage_stores_items_per_page');
        }
        $columnId = $this->getColumnIdByName('Store View Name');
        $storeViews = array();
        $this->addParameter('tableHeadXpath', $this->_getControlXpath('pageelement', 'stores_table'));
        $elements = $this->getControlElements('pageelement', 'table_line');
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $element
         */
        foreach ($elements as $key => $element) {
            $storeViews[$key] = trim($this->getChildElement($element, "td[$columnId]")->text());
        }
        $storeViews = array_diff($storeViews, $excludeList);
        foreach ($storeViews as $storeView) {
            $this->deleteStore(array('store_view_name' => $storeView));
        }
    }
}

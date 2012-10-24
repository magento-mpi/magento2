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
class Enterprise2_Mage_Store_Helper extends Core_Mage_Store_Helper
{
    /**
     * Create Status Order
     *
     * Preconditions: 'New Order Status' page is opened.
     *
     * @param array|string $data
     *
     */
    public function createStatus($data)
    {
        if (is_string($data)) {
            $elements = explode('/', $data);
            $fileName = (count($elements) > 1)
                ? array_shift($elements)
                : '';
            $data = $this->loadDataSet($fileName, implode('/', $elements));
        }

        $this->clickButton('create_new_status');
        $this->fillFieldSet($data, 'order_status_info');
        $this->saveForm('save_status');
    }

    /**
     * Assign Order Status new state values
     *
     * Preconditions: 'Order statuses' page is opened.
     * @param array|string $data
     *
     */
    public function assignStatus($data)
    {
        if (is_string($data)) {
            $elements = explode('/', $data);
            $fileName = (count($elements) > 1)
                ? array_shift($elements)
                : '';
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
    public function deleteStoreViewsExceptSpecified(array $excludeList)
    {
        $tableXpath = $this->_getControlXpath('pageelement', 'stores_table');
        $titleRowCount = $this->getXpathCount($tableXpath . '//tr[@title]');
        $columnId = $this->getColumnIdByName('Store View Name') - 1;
        $storeViews = array();
        for ($rowId = 0; $rowId < $titleRowCount; $rowId++) {
            $storeView = $this->getTable($tableXpath . '.' . $rowId . '.' . $columnId);
            if (!in_array($storeView, $excludeList)) {
                $storeViews[] = $storeView;
            }
        }
        foreach ($storeViews as $storeView) {
            $this->storeHelper()->deleteStore(array('store_view_name' => $storeView));
        }
    }
}

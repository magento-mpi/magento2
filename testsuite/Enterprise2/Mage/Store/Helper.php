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
 * @method Community2_Mage_Store_Helper helper(string $className)
 */
class Enterprise2_Mage_Store_Helper extends Core_Mage_Store_Helper
{
    /**
     * Create Status Order
     * Preconditions: 'New Order Status' page is opened.
     *
     * @param array|string $data
     */
    public function createStatus($data)
    {
        $this->helper('Community2/Mage/Store/Helper')->createStatus($data);
    }

    /**
     * Assign Order Status new state values
     * Preconditions: 'Order statuses' page is opened.
     *
     * @param array|string $data
     */
    public function assignStatus($data)
    {
        $this->helper('Community2/Mage/Store/Helper')->assignStatus($data);
    }

    /**
     * Delete all Store Views except specified in $excludeList
     *
     * @param array $excludeList
     */
    public function deleteStoreViewsExceptSpecified(array $excludeList = array('Default Store View'))
    {
        $this->helper('Community2/Mage/Store/Helper')->deleteStoreViewsExceptSpecified($excludeList);
    }
}

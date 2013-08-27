<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_Core_Model_StoreManagerInterface extends Magento_Core_Model_Store_ListInterface
{

    /**#@+
     * Available scope types
     */
    const SCOPE_TYPE_STORE   = 'store';
    const SCOPE_TYPE_GROUP   = 'group';
    const SCOPE_TYPE_WEBSITE = 'website';
    /**#@-*/

    /**
     * Retrieve application store object without Store_Exception
     *
     * @param string|int|Magento_Core_Model_Store $storeId
     * @return Magento_Core_Model_Store
     */
    public function getSafeStore($storeId = null);

    /**
     * Check if system is run in the single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode();
}

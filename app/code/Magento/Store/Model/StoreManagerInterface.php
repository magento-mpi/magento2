<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Store\Model;

interface StoreManagerInterface extends \Magento\Store\Model\Store\ListInterface
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
     * @param string|int|Store $storeId
     * @return Store
     */
    public function getSafeStore($storeId = null);

    /**
     * Check if system is run in the single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode();
}

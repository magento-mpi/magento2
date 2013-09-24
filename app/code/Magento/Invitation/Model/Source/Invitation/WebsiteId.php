<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation websites options source
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Magento_Invitation_Model_Source_Invitation_WebsiteId implements Magento_Core_Model_Option_ArrayInterface

{
    /**
     * Store
     *
     * @var Magento_Core_Model_System_Store
     */
    protected $_store;

    /**
     * @param Magento_Core_Model_System_Store $store
     */
    function __construct(
            Magento_Core_Model_System_Store $store
    ) {
        $this->_store = $store;
    }

    /**
     * Return list of invitation statuses as options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return  $this->_store->getWebsiteOptionHash();
    }
}

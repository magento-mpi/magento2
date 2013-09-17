<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GoogleCheckout_Model_Resource_Api_Debug_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection constructor
     */
    protected function _construct()
    {
        $this->_init('Magento_GoogleCheckout_Model_Api_Debug', 'Magento_GoogleCheckout_Model_Resource_Api_Debug');
    }
}

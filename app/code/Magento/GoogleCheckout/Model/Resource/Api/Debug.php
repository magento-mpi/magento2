<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GoogleCheckout_Model_Resource_Api_Debug extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource constructor
     */
    protected function _construct()
    {
        $this->_init('googlecheckout_api_debug', 'debug_id');
    }
}

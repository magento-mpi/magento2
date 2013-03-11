<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enter description here ...
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleCheckout_Model_Resource_Api_Debug extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource constructor
     *
     */
    protected function _construct()
    {
        $this->_init('googlecheckout_api_debug', 'debug_id');
    }
}

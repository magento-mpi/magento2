<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_GoogleCheckout_Model_Resource_Api_Debug_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection constructor
     */
    protected function _construct()
    {
        $this->_init('Mage_GoogleCheckout_Model_Api_Debug', 'Mage_GoogleCheckout_Model_Resource_Api_Debug');
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Resource authorizenet debug model
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paygate_Model_Resource_Authorizenet_Debug extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('paygate_authorizenet_debug', 'debug_id');
    }
}

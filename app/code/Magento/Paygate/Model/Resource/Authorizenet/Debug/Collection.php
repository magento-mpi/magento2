<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paygate
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Resource authorizenet debug collection model
 *
 * @category    Magento
 * @package     Magento_Paygate
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paygate_Model_Resource_Authorizenet_Debug_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Paygate_Model_Authorizenet_Debug', 'Magento_Paygate_Model_Resource_Authorizenet_Debug');
    }
}

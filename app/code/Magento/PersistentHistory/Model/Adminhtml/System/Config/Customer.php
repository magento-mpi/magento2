<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PersistentHistory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise Persistent System Config Shopping Customer option backend model
 *
 */
class Magento_PersistentHistory_Model_Adminhtml_System_Config_Customer extends Magento_Core_Model_Config_Value
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_persistenthistory_options_customer';

    /**
     * Processing object before save data
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $groups = $this->getGroups();
        if (isset($groups['options']['fields']['shopping_cart']['value'])
            && $groups['options']['fields']['shopping_cart']['value'] ) {
            $this->_dataSaveAllowed = false;
            return $this;
        }

        return parent::_beforeSave();
    }
}

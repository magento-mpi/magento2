<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise Persistent System Config Shopping Customer option backend model
 *
 */
class Enterprise_Persistent_Model_Adminhtml_System_Config_Customer extends Magento_Core_Model_Config_Data
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'enterprise_persistent_options_customer';

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

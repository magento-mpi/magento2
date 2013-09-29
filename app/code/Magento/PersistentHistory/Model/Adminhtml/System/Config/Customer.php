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
namespace Magento\PersistentHistory\Model\Adminhtml\System\Config;

class Customer extends \Magento\Core\Model\Config\Value
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
     * @return \Magento\Core\Model\AbstractModel
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

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * AdminNotification Inbox model
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdminNotification_Model_Resource_Grid_Collection
    extends Magento_AdminNotification_Model_Resource_Inbox_Collection
{

    /**
     * Add remove filter
     *
     * @return Magento_AdminNotification_Model_Resource_Grid_Collection|Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addRemoveFilter();
        return $this;
    }
}

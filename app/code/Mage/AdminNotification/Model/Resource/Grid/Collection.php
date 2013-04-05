<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * AdminNotification Inbox model
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AdminNotification_Model_Resource_Grid_Collection
    extends Mage_AdminNotification_Model_Resource_Inbox_Collection
{

    /**
     * Add remove filter
     *
     * @return Mage_AdminNotification_Model_Resource_Grid_Collection|Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addRemoveFilter();
        return $this;
    }
}

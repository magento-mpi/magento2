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
class Mage_AdminNotification_Model_Resource_Inbox_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_AdminNotification_Model_Inbox', 'Mage_AdminNotification_Model_Resource_Inbox');
    }

    /**
     * Add remove filter
     *
     * @return Mage_AdminNotification_Model_Resource_Inbox_Collection
     */
    public function addRemoveFilter()
    {
        $this->getSelect()
            ->where('is_remove=?', 0);
        return $this;
    }
}

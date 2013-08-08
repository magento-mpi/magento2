<?php
/**
 * {license_notice}
 *
 * @category    Enterise
 * @package     Enterpise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reminder data grid collection
 *
 * @category    Enterise
 * @package     Enterpise_Reminder
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reminder_Model_Resource_Reminder_Collection
    extends Enterprise_Reminder_Model_Resource_Rule_Collection
{
    /**
     * @return Enterprise_Reminder_Model_Resource_Reminder_Collection|Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }
}
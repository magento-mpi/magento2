<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Resource helper class for Oracle Varien DB Adapter
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reminder_Model_Resource_Helper_Oracle extends Mage_Core_Model_Resource_Helper_Oracle
{

    /**
     * Sets limit for rules specific select
     *
     * @param Varien_Db_Select $select
     * @param int $limit
     * @return void
     */
    public function setRuleLimit(Varien_Db_Select $select, $limit)
    {
        $select->where('ROWNUM <= ?', $limit);
    }
}
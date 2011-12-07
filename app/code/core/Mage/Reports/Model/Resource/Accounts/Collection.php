<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * New Accounts Report collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Accounts_Collection extends Mage_Reports_Model_Resource_Customer_Collection
{

    /**
     * Join created_at and accounts fields
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Accounts_Collection
     */
    protected function _joinFields($from = '', $to = '')
    {

        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $this->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to, 'datetime' => true))
             ->addExpressionAttributeToSelect('accounts', 'COUNT({{entity_id}})', array('entity_id'));

        $this->getSelect()->having("{$this->_joinFields['accounts']['field']} > ?", 0);

        return $this;
    }

    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Accounts_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
             ->_joinFields($from, $to);
        return $this;
    }

    /**
     * Set store ids to final result
     *
     * @param array $storeIds
     * @return Mage_Reports_Model_Resource_Accounts_Collection
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addAttributeToFilter('store_id', array('in' => (array)$storeIds));
        }
        return $this;
    }
}

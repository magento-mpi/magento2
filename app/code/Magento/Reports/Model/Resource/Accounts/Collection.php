<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * New Accounts Report collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Resource\Accounts;

class Collection extends \Magento\Reports\Model\Resource\Customer\Collection
{

    /**
     * Join created_at and accounts fields
     *
     * @param string $fromDate
     * @param string $toDate
     * @return \Magento\Reports\Model\Resource\Accounts\Collection
     */
    protected function _joinFields($fromDate = '', $toDate = '')
    {

        $this->getSelect()->reset(\Zend_Db_Select::COLUMNS);
        $this->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate, 'datetime' => true))
             ->addExpressionAttributeToSelect('accounts', 'COUNT({{entity_id}})', array('entity_id'));

        $this->getSelect()->having("{$this->_joinFields['accounts']['field']} > ?", 0);

        return $this;
    }

    /**
     * Set date range
     *
     * @param string $fromDate
     * @param string $toDate
     * @return \Magento\Reports\Model\Resource\Accounts\Collection
     */
    public function setDateRange($fromDate, $toDate)
    {
        $this->_reset()
             ->_joinFields($fromDate, $toDate);
        return $this;
    }

    /**
     * Set store ids to final result
     *
     * @param array $storeIds
     * @return \Magento\Reports\Model\Resource\Accounts\Collection
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addAttributeToFilter('store_id', array('in' => (array)$storeIds));
        }
        return $this;
    }
}

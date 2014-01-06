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
 * Report collection abstract model
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Resource\Report\Collection;

class AbstractCollection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * From date
     *
     * @var string
     */
    protected $_from               = null;

    /**
     * To date
     *
     * @var string
     */
    protected $_to                 = null;

    /**
     * Period
     *
     * @var string
     */
    protected $_period             = null;

    /**
     * Store ids
     *
     * @var int|array
     */
    protected $_storesIds          = 0;

    /**
     * Is totals
     *
     * @var bool
     */
    protected $_isTotals           = false;

    /**
     * Is subtotals
     *
     * @var bool
     */
    protected $_isSubTotals        = false;

    /**
     * Aggregated columns
     *
     * @var array
     */
    protected $_aggregatedColumns  = array();

    /**
     * Set array of columns that should be aggregated
     *
     * @param array $columns
     * @return \Magento\Sales\Model\Resource\Report\Collection\AbstractCollection
     */
    public function setAggregatedColumns(array $columns)
    {
        $this->_aggregatedColumns = $columns;
        return $this;
    }

    /**
     * Retrieve array of columns that should be aggregated
     *
     * @return array
     */
    public function getAggregatedColumns()
    {
        return $this->_aggregatedColumns;
    }

    /**
     * Set date range
     *
     * @param mixed $from
     * @param mixed $to
     * @return \Magento\Sales\Model\Resource\Report\Collection\AbstractCollection
     */
    public function setDateRange($from = null, $to = null)
    {
        $this->_from = $from;
        $this->_to   = $to;
        return $this;
    }

    /**
     * Set period
     *
     * @param string $period
     * @return \Magento\Sales\Model\Resource\Report\Collection\AbstractCollection
     */
    public function setPeriod($period)
    {
        $this->_period = $period;
        return $this;
    }

    /**
     * Apply date range filter
     *
     * @return \Magento\Sales\Model\Resource\Report\Collection\AbstractCollection
     */
    protected function _applyDateRangeFilter()
    {
        // Remember that field PERIOD is a DATE(YYYY-MM-DD) in all databases
        if ($this->_from !== null) {
            $this->getSelect()->where('period >= ?', $this->_from);
        }
        if ($this->_to !== null) {
            $this->getSelect()->where('period <= ?', $this->_to);
        }

        return $this;
    }

    /**
     * Set store ids
     *
     * @param mixed $storeIds (null, int|string, array, array may contain null)
     * @return \Magento\Sales\Model\Resource\Report\Collection\AbstractCollection
     */
    public function addStoreFilter($storeIds)
    {
        $this->_storesIds = $storeIds;
        return $this;
    }

    /**
     * Apply stores filter to select object
     *
     * @param \Zend_Db_Select $select
     * @return \Magento\Sales\Model\Resource\Report\Collection\AbstractCollection
     */
    protected function _applyStoresFilterToSelect(\Zend_Db_Select $select)
    {
        $nullCheck = false;
        $storeIds  = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        if ($nullCheck) {
            $select->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        } else {
            $select->where('store_id IN(?)', $storeIds);
        }

        return $this;
    }

    /**
     * Apply stores filter
     *
     * @return \Magento\Sales\Model\Resource\Report\Collection\AbstractCollection
     */
    protected function _applyStoresFilter()
    {
        return $this->_applyStoresFilterToSelect($this->getSelect());
    }

    /**
     * Getter/Setter for isTotals
     *
     * @param null|boolean $flag
     * @return \Magento\Sales\Model\Resource\Report\Collection\AbstractCollection
     */
    public function isTotals($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isTotals;
        }
        $this->_isTotals = $flag;
        return $this;
    }

    /**
     * Getter/Setter for isSubTotals
     *
     * @param null|boolean $flag
     * @return \Magento\Sales\Model\Resource\Report\Collection\AbstractCollection
     */
    public function isSubTotals($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isSubTotals;
        }
        $this->_isSubTotals = $flag;
        return $this;
    }

    /**
     * Custom filters application ability
     *
     * @return \Magento\Reports\Model\Resource\Report\Collection\AbstractCollection
     */
    protected function _applyCustomFilter()
    {
        return $this;
    }

    /**
     * Apply filters common to reports
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected function _initSelect()
    {
        $this->_applyDateRangeFilter();
        $this->_applyStoresFilter();
        $this->_applyCustomFilter();
        return $this;
    }
}

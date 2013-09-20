<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales report coupons collection
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesRule_Model_Resource_Report_Collection extends Magento_Sales_Model_Resource_Report_Collection_Abstract
{
    /**
     * Period format for report (day, month, year)
     *
     * @var string
     */
    protected $_periodFormat;

    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_aggregationTable = 'coupon_aggregated';

    /**
     * array of columns that should be aggregated
     *
     * @var array
     */
    protected $_selectedColumns    = array();

    /**
     * array where rules ids stored
     *
     * @var array
     */
    protected $_rulesIdsFilter;

    /**
     * @var Magento_SalesRule_Model_Resource_Report_Rule
     */
    protected $_reportRule;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Sales_Model_Resource_Report $resource
     * @param Magento_SalesRule_Model_Resource_Report_Rule $reportRule
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Sales_Model_Resource_Report $resource,
        Magento_SalesRule_Model_Resource_Report_Rule $reportRule
    ) {
        $resource->init($this->_aggregationTable);
        $this->_reportRule = $reportRule;
        parent::__construct($eventManager, $fetchStrategy, $resource);
    }

    /**
     * collect columns for collection
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $adapter = $this->getConnection();
        if ('month' == $this->_period) {
            $this->_periodFormat = $adapter->getDateFormatSql('period', '%Y-%m');
        } elseif ('year' == $this->_period) {
            $this->_periodFormat =
                $adapter->getDateExtractSql('period', Magento_DB_Adapter_Interface::INTERVAL_YEAR);
        } else {
            $this->_periodFormat = $adapter->getDateFormatSql('period', '%Y-%m-%d');
        }

        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->_selectedColumns = array(
                'period'                  => $this->_periodFormat,
                'coupon_code',
                'rule_name',
                'coupon_uses'             => 'SUM(coupon_uses)',
                'subtotal_amount'         => 'SUM(subtotal_amount)',
                'discount_amount'         => 'SUM(discount_amount)',
                'total_amount'            => 'SUM(total_amount)',
                'subtotal_amount_actual'  => 'SUM(subtotal_amount_actual)',
                'discount_amount_actual'  => 'SUM(discount_amount_actual)',
                'total_amount_actual'     => 'SUM(total_amount_actual)',
            );
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        if ($this->isSubTotals()) {
            $this->_selectedColumns =
                $this->getAggregatedColumns() +
                    array('period' => $this->_periodFormat);
        }

        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     *
     * @return Magento_SalesRule_Model_Resource_Report_Collection
     */
    protected function _initSelect()
    {
        $this->getSelect()->from($this->getResource()->getMainTable(), $this->_getSelectedColumns());
        if ($this->isSubTotals()) {
            $this->getSelect()->group($this->_periodFormat);
        } else if (!$this->isTotals()) {
            $this->getSelect()->group(array(
                $this->_periodFormat,
                'coupon_code'
            ));
        }

        return parent::_initSelect();
    }

    /**
     * Add filtering by rules ids
     *
     * @param array $rulesList
     * @return Magento_SalesRule_Model_Resource_Report_Collection
     */
    public function addRuleFilter($rulesList)
    {
        $this->_rulesIdsFilter = $rulesList;
        return $this;
    }

    /**
     * Apply filtering by rules ids
     *
     * @return Magento_SalesRule_Model_Resource_Report_Collection
     */
    protected function _applyRulesFilter()
    {
        if (empty($this->_rulesIdsFilter) || !is_array($this->_rulesIdsFilter)) {
            return $this;
        }

        $rulesList = $this->_reportRule->getUniqRulesNamesList();
        $rulesFilterSqlParts = array();
        foreach ($this->_rulesIdsFilter as $ruleId) {
            if (!isset($rulesList[$ruleId])) {
                continue;
            }
            $ruleName = $rulesList[$ruleId];
            $rulesFilterSqlParts[] = $this->getConnection()->quoteInto('rule_name = ?', $ruleName);
        }

        if (!empty($rulesFilterSqlParts)) {
            $this->getSelect()->where(implode($rulesFilterSqlParts, ' OR '));
        }
        return $this;
    }

    /**
     * Apply collection custom filter
     *
     * @return Magento_Sales_Model_Resource_Report_Collection_Abstract
     */
    protected function _applyCustomFilter()
    {
        $this->_applyRulesFilter();
        return parent::_applyCustomFilter();
    }
}

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
 * Rule report resource model
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesRule_Model_Resource_Report_Rule extends Magento_Reports_Model_Resource_Report_Abstract
{
    /**
     * @var Magento_SalesRule_Model_Resource_Report_Rule_Createdat
     */
    protected $_ruleCreated;

    /**
     * @var Magento_SalesRule_Model_Resource_Report_Rule_Updatedat
     */
    protected $_ruleUpdated;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_SalesRule_Model_Resource_Report_Rule_Createdat $ruleCreated
     * @param Magento_SalesRule_Model_Resource_Report_Rule_Updatedat $ruleUpdated
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Resource $resource,
        Magento_SalesRule_Model_Resource_Report_Rule_Createdat $ruleCreated,
        Magento_SalesRule_Model_Resource_Report_Rule_Updatedat $ruleUpdated
    ) {
        parent::__construct($logger, $resource);
        $this->_ruleCreated = $ruleCreated;
        $this->_ruleUpdated = $ruleUpdated;
    }

    /**
     * Resource Report Rule constructor
     */
    protected function _construct()
    {
        $this->_setResource('salesrule');
    }

    /**
     * Aggregate Coupons data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Magento_SalesRule_Model_Resource_Report_Rule
     */
    public function aggregate($from = null, $to = null)
    {
        $this->_ruleCreated->aggregate($from, $to);
        $this->_ruleUpdated->aggregate($from, $to);
        $this->_setFlagData(Magento_Reports_Model_Flag::REPORT_COUPONS_FLAG_CODE);

        return $this;
    }

    /**
     * Get all unique Rule Names from aggregated coupons usage data
     *
     * @return array
     */
    public function getUniqRulesNamesList()
    {
        $adapter = $this->_getReadAdapter();
        $tableName = $this->getTable('coupon_aggregated');
        $select = $adapter->select()
            ->from(
                $tableName,
                new Zend_Db_Expr('DISTINCT rule_name')
            )
            ->where('rule_name IS NOT NULL')
            ->where('rule_name <> ?', '')
            ->order('rule_name ASC');

        $rulesNames = $adapter->fetchAll($select);

        $result = array();

        foreach ($rulesNames as $row) {
            $result[] = $row['rule_name'];
        }

        return $result;
    }
}

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
namespace Magento\SalesRule\Model\Resource\Report;

class Rule extends \Magento\Reports\Model\Resource\Report\AbstractReport
{
    /**
     * Resource Report Rule constructor
     *
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
     * @return \Magento\SalesRule\Model\Resource\Report\Rule
     */
    public function aggregate($from = null, $to = null)
    {
        \Mage::getResourceModel('Magento\SalesRule\Model\Resource\Report\Rule\Createdat')->aggregate($from, $to);
        \Mage::getResourceModel('Magento\SalesRule\Model\Resource\Report\Rule\Updatedat')->aggregate($from, $to);
        $this->_setFlagData(\Magento\Reports\Model\Flag::REPORT_COUPONS_FLAG_CODE);

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
                new \Zend_Db_Expr('DISTINCT rule_name')
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

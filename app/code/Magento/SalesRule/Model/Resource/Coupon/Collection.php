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
 * SalesRule Model Resource Coupon_Collection
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesRule_Model_Resource_Coupon_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_SalesRule_Model_Coupon', 'Magento_SalesRule_Model_Resource_Coupon');
    }

    /**
     * Add rule to filter
     *
     * @param Magento_SalesRule_Model_Rule|int $rule
     *
     * @return Magento_SalesRule_Model_Resource_Coupon_Collection
     */
    public function addRuleToFilter($rule)
    {
        if ($rule instanceof Magento_SalesRule_Model_Rule) {
            $ruleId = $rule->getId();
        } else {
            $ruleId = (int)$rule;
        }

        $this->addFieldToFilter('rule_id', $ruleId);

        return $this;
    }

    /**
     * Add rule IDs to filter
     *
     * @param array $ruleIds
     *
     * @return Magento_SalesRule_Model_Resource_Coupon_Collection
     */
    public function addRuleIdsToFilter(array $ruleIds)
    {
        $this->addFieldToFilter('rule_id', array('in' => $ruleIds));
        return $this;
    }

    /**
     * Filter collection to be filled with auto-generated coupons only
     *
     * @return Magento_SalesRule_Model_Resource_Coupon_Collection
     */
    public function addGeneratedCouponsFilter()
    {
        $this->addFieldToFilter('is_primary', array('null' => 1))->addFieldToFilter('type', '1');
        return $this;
    }

    /**
     * Callback function that filters collection by field "Used" from grid
     *
     * @param Magento_Core_Model_Resource_Db_Collection_Abstract $collection
     * @param Magento_Adminhtml_Block_Widget_Grid_Column $column
     */
    public function addIsUsedFilterCallback($collection, $column)
    {
        $filterValue = $column->getFilter()->getCondition();

        $expression = $this->getConnection()->getCheckSql('main_table.times_used > 0', 1, 0);
        $conditionSql = $this->_getConditionSql($expression, $filterValue);
        $collection->getSelect()->where($conditionSql);
    }
}

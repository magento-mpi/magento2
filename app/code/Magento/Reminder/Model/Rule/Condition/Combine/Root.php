<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Root rule condition (top level condition)
 */
namespace Magento\Reminder\Model\Rule\Condition\Combine;

class Root
    extends \Magento\Reminder\Model\Rule\Condition\Combine
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento\Reminder\Model\Rule\Condition\Combine\Root');
    }

    /**
     * Prepare base select with limitation by customer
     *
     * @param   null | array | int | \Magento\Customer\Model\Customer $customer
     * @param   int | \Zend_Db_Expr $website
     * @return  \Magento\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $rootTable = $this->getResource()->getTable('customer_entity');
        $couponTable = $this->getResource()->getTable('magento_reminder_rule_coupon');

        $select->from(array('root' => $rootTable), array('entity_id'));

        $select->joinLeft(
            array('c' => $couponTable),
            'c.customer_id=root.entity_id AND c.rule_id=:rule_id',
            array('c.coupon_id')
        );

        if ($customer === null) {
            if (\Mage::getSingleton('Magento\Customer\Model\Config\Share')->isWebsiteScope()) {
                $select->where('website_id=?', $website);
            }
        }
        return $select;
    }

    /**
     * Get SQL select.
     * Rewrited for cover root conditions combination with additional condition by customer
     *
     * @param   \Magento\Customer\Model\Customer | \Zend_Db_Select | \Zend_Db_Expr $customer
     * @param   int | \Zend_Db_Expr $website
     * @return  \Magento\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $select     = $this->_prepareConditionsSql($customer, $website);
        $required   = $this->_getRequiredValidation();
        $aggregator = ($this->getAggregator() == 'all') ? ' AND ' : ' OR ';
        $operator   = $required ? '=' : '<>';
        $conditions = array();

        foreach ($this->getConditions() as $condition) {
            $sql = $condition->getConditionsSql($customer, $website);
            if ($sql) {
                $conditions[] =  '(' . $select->getAdapter()->getIfNullSql("(" . $sql . ")", 0) . " {$operator} 1)";
            }
        }

        if (!empty($conditions)) {
            $select->where(implode($aggregator, $conditions));
        } else {
            $select->reset();
        }

        return $select;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Rule\Condition\Combine;

use Magento\Customer\Model\Customer;
use Magento\Framework\DB\Select;

/**
 * Root rule condition (top level condition)
 */
class Root extends \Magento\Reminder\Model\Rule\Condition\Combine
{
    /**
     * Config
     *
     * @var \Magento\Customer\Model\Config\Share
     */
    protected $_config;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Reminder\Model\Resource\Rule $ruleResource
     * @param \Magento\Customer\Model\Config\Share $config
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Reminder\Model\Resource\Rule $ruleResource,
        \Magento\Customer\Model\Config\Share $config,
        array $data = []
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento\Reminder\Model\Rule\Condition\Combine\Root');
        $this->_config = $config;
    }

    /**
     * Prepare base select with limitation by customer
     *
     * @param null|array|int|Customer $customer
     * @param int|\Zend_Db_Expr $website
     * @return Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $rootTable = $this->getResource()->getTable('customer_entity');
        $couponTable = $this->getResource()->getTable('magento_reminder_rule_coupon');

        $select->from(['root' => $rootTable], ['entity_id']);

        $select->joinLeft(
            ['c' => $couponTable],
            'c.customer_id=root.entity_id AND c.rule_id=:rule_id',
            ['c.coupon_id']
        );

        if ($customer === null) {
            if ($this->_config->isWebsiteScope()) {
                $select->where('website_id=?', $website);
            }
        }
        return $select;
    }

    /**
     * Get SQL select.
     *
     * Rewritten for cover root conditions combination with additional condition by customer
     *
     * @param Customer|\Zend_Db_Select|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return Select
     */
    public function getConditionsSql($customer, $website)
    {
        $select = $this->_prepareConditionsSql($customer, $website);
        $required = $this->_getRequiredValidation();
        $aggregator = $this->getAggregator() == 'all' ? ' AND ' : ' OR ';
        $operator = $required ? '=' : '<>';
        $conditions = [];

        foreach ($this->getConditions() as $condition) {
            $sql = $condition->getConditionsSql($customer, $website);
            if ($sql) {
                $conditions[] = '(' . $select->getAdapter()->getIfNullSql("(" . $sql . ")", 0) . " {$operator} 1)";
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

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
class Magento_Reminder_Model_Rule_Condition_Combine_Root
    extends Magento_Reminder_Model_Rule_Condition_Combine
{
    /**
     * Config
     *
     * @var Magento_Customer_Model_Config_Share
     */
    protected $_config;

    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_Reminder_Model_Resource_Rule $ruleResource
     * @param Magento_Customer_Model_Config_Share $config
     * @param array $data
     */
    public function __construct(
        Magento_Rule_Model_Condition_Context $context,
        Magento_Reminder_Model_Resource_Rule $ruleResource,
        Magento_Customer_Model_Config_Share $config,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento_Reminder_Model_Rule_Condition_Combine_Root');
        $this->_config = $config;
    }

    /**
     * Prepare base select with limitation by customer
     *
     * @param   null | array | int | Magento_Customer_Model_Customer $customer
     * @param   int | Zend_Db_Expr $website
     * @return  Magento_DB_Select
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
            if ($this->_config->isWebsiteScope()) {
                $select->where('website_id=?', $website);
            }
        }
        return $select;
    }

    /**
     * Get SQL select.
     * Rewrited for cover root conditions combination with additional condition by customer
     *
     * @param   Magento_Customer_Model_Customer | Zend_Db_Select | Zend_Db_Expr $customer
     * @param   int | Zend_Db_Expr $website
     * @return  Magento_DB_Select
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

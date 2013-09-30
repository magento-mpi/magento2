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
 * Customer cart conditions combine
 */
class Magento_Reminder_Model_Rule_Condition_Cart
    extends Magento_Reminder_Model_Condition_Combine_Abstract
{
    /**
     * @var Magento_Core_Model_Date
     */
    protected $_dateModel;

    /**
     * Core resource helper
     *
     * @var Magento_Reminder_Model_Resource_HelperFactory
     */
    protected $_resHelperFactory;

    /**
     * Cart Combine Factory
     *
     * @var Magento_Reminder_Model_Rule_Condition_Cart_CombineFactory
     */
    protected $_combineFactory;

    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_Reminder_Model_Resource_Rule $ruleResource
     * @param Magento_Core_Model_Date $dateModel
     * @param Magento_Reminder_Model_Resource_HelperFactory $resHelperFactory
     * @param Magento_Reminder_Model_Rule_Condition_Cart_CombineFactory $combineFactory
     * @param array $data
     */
    public function __construct(
        Magento_Rule_Model_Condition_Context $context,
        Magento_Reminder_Model_Resource_Rule $ruleResource,
        Magento_Core_Model_Date $dateModel,
        Magento_Reminder_Model_Resource_HelperFactory $resHelperFactory,
        Magento_Reminder_Model_Rule_Condition_Cart_CombineFactory $combineFactory,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->_dateModel = $dateModel;
        $this->setType('Magento_Reminder_Model_Rule_Condition_Cart');
        $this->setValue(null);
        $this->_resHelperFactory = $resHelperFactory;
        $this->_combineFactory = $combineFactory;
    }

    /**
     * Get list of available subconditions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return $this->_combineFactory->create()->getNewChildSelectOptions();
    }

    /**
     * Get input type for attribute value
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Override parent method
     *
     * @return Magento_Reminder_Model_Rule_Condition_Cart
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array());
        return $this;
    }

    /**
     * Prepare operator select options
     *
     * @return Magento_Reminder_Model_Rule_Condition_Cart
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '==' => __('for'),
            '>'  => __('for greater than'),
            '>=' => __('for or greater than')
        ));
        return $this;
    }

    /**
     * Return required validation
     *
     * @return bool
     */
    protected function _getRequiredValidation()
    {
        return true;
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Shopping cart is not empty and abandoned %1 %2 days and %3 of these conditions match:',
                $this->getOperatorElementHtml(), $this->getValueElementHtml(), $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get condition SQL select
     *
     * @param   int|Zend_Db_Expr $customer
     * @param   int|Zend_Db_Expr $website
     * @return  Magento_DB_Select
     * @throws Magento_Core_Exception
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $conditionValue = (int)$this->getValue();
        if ($conditionValue < 0) {
            throw new Magento_Core_Exception(
                __('The root shopping cart condition should have a days value of 0 or greater.')
            );
        }

        $table = $this->getResource()->getTable('sales_flat_quote');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('quote' => $table), array(new Zend_Db_Expr(1)));

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');

        $currentTime = $this->_dateModel->gmtDate('Y-m-d');

        /** @var $helper Magento_Core_Model_Resource_Helper */
        $helper = $this->_resHelperFactory->create();
        $daysDiffSql = $helper->getDateDiff(
            'quote.updated_at', $select->getAdapter()->formatDate($currentTime)
        );
        if ($operator == '=') {
            $select->where($daysDiffSql . ' < ?', $conditionValue);
            $select->where($daysDiffSql . ' > ?', $conditionValue - 1);
        } else {
            if ($operator == '>=' && $conditionValue == 0) {
                $currentTime = $this->_dateModel->gmtDate();
                $daysDiffSql = $helper->getDateDiff(
                    'quote.updated_at', $select->getAdapter()->formatDate($currentTime)
                );
            }
            $select->where($daysDiffSql . " {$operator} ?", $conditionValue);
        }

        $select->where('quote.is_active = 1');
        $select->where('quote.items_count > 0');
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        $select->limit(1);
        return $select;
    }

    /**
     * Get base SQL select
     *
     * @param   int|Zend_Db_Expr $customer
     * @param   int|Zend_Db_Expr $website
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
                $conditions[] = "(" . $select->getAdapter()->getIfNullSql("(" . $sql . ")", 0) . " {$operator} 1)";
            }
        }

        if (!empty($conditions)) {
            $select->where(implode($aggregator, $conditions));
        }

        return $select;
    }
}

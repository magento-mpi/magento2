<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Rule\Condition;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\Exception;

/**
 * Customer cart conditions combine
 */
class Cart extends \Magento\Reminder\Model\Condition\Combine\AbstractCombine
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateModel;

    /**
     * Core resource helper
     *
     * @var \Magento\Framework\DB\Helper
     */
    protected $_resourceHelper;

    /**
     * Cart Combine Factory
     *
     * @var \Magento\Reminder\Model\Rule\Condition\Cart\CombineFactory
     */
    protected $_combineFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Reminder\Model\Resource\Rule $ruleResource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateModel
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param \Magento\Reminder\Model\Rule\Condition\Cart\CombineFactory $combineFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Reminder\Model\Resource\Rule $ruleResource,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateModel,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Reminder\Model\Rule\Condition\Cart\CombineFactory $combineFactory,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->_dateModel = $dateModel;
        $this->setType('Magento\Reminder\Model\Rule\Condition\Cart');
        $this->setValue(null);
        $this->_resourceHelper = $resourceHelper;
        $this->_combineFactory = $combineFactory;
    }

    /**
     * Get list of available sub conditions
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
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array());
        return $this;
    }

    /**
     * Prepare operator select options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            array('==' => __('for'), '>' => __('for greater than'), '>=' => __('for or greater than'))
        );
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
        return $this->getTypeElementHtml() . __(
            'Shopping cart is not empty and abandoned %1 %2 days and %3 of these conditions match:',
            $this->getOperatorElementHtml(),
            $this->getValueElementHtml(),
            $this->getAggregatorElement()->getHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Get condition SQL select
     *
     * @param null|int|Zend_Db_Expr $customer
     * @param int|Zend_Db_Expr $website
     * @return Select
     * @throws Exception
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $conditionValue = (int)$this->getValue();
        if ($conditionValue < 0) {
            throw new \Magento\Framework\Model\Exception(
                __('The root shopping cart condition should have a days value of 0 or greater.')
            );
        }

        $table = $this->getResource()->getTable('sales_quote');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('quote' => $table), array(new \Zend_Db_Expr(1)));

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');

        $currentTime = $this->_dateModel->gmtDate('Y-m-d');

        $daysDiffSql = $this->_resourceHelper->getDateDiff(
            'quote.updated_at',
            $select->getAdapter()->formatDate($currentTime)
        );
        if ($operator == '>=' && $conditionValue == 0) {
            $currentTime = $this->_dateModel->gmtDate();
            $daysDiffSql = $this->_resourceHelper->getDateDiff(
                'quote.updated_at',
                $select->getAdapter()->formatDate($currentTime)
            );
        }
        $select->where($daysDiffSql . " {$operator} ?", $conditionValue);
        $select->where('quote.is_active = 1');
        $select->where('quote.items_count > 0');
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        $select->limit(1);
        return $select;
    }

    /**
     * Get base SQL select
     *
     * @param null|int|Zend_Db_Expr $customer
     * @param int|Zend_Db_Expr $website
     * @return Select
     */
    public function getConditionsSql($customer, $website)
    {
        $select = $this->_prepareConditionsSql($customer, $website);
        $required = $this->_getRequiredValidation();
        $aggregator = $this->getAggregator() == 'all' ? ' AND ' : ' OR ';
        $operator = $required ? '=' : '<>';
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

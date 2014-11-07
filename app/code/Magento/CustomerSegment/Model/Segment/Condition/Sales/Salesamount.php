<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Sales;

use Magento\Customer\Model\Customer;
use Zend_Db_Expr;

/**
 * Orders amount condition
 */
class Salesamount extends \Magento\CustomerSegment\Model\Segment\Condition\Sales\Combine
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        array $data = array()
    ) {
        parent::__construct($context, $conditionFactory, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Sales\Salesamount');
        $this->setValue(null);
    }

    /**
     * Set data with filtering
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        //filter key "value"
        if (is_array($key) && isset($key['value']) && $key['value'] !== null) {
            $key['value'] = (double)$key['value'];
        } elseif ($key == 'value' && $value !== null) {
            $value = (double)$value;
        }

        return parent::setData($key, $value);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        return array('sales_order_save_commit_after');
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml() . __(
            '%1 Sales Amount %2 %3 while %4 of these Conditions match:',
            $this->getAttributeElementHtml(),
            $this->getOperatorElementHtml(),
            $this->getValueElementHtml(),
            $this->getAggregatorElement()->getHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Build query for matching orders amount
     *
     * @param Customer| Zend_Db_Expr $customer
     * @param int|Zend_Db_Expr $website
     * @return \Magento\Framework\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();

        $operator = $this->getResource()->getSqlOperator($this->getOperator());
        $aggrFunc = $this->getAttribute() == 'total' ? 'SUM' : 'AVG';
        $adapter = $this->getResource()->getReadConnection();
        $firstIf = $adapter->getCheckSql(
            $aggrFunc . '(sales_order.base_grand_total) IS NOT NULL',
            $aggrFunc . '(sales_order.base_grand_total)',
            0
        );
        $value = (double)$this->getValue();
        $result = $adapter->getCheckSql($firstIf . ' ' . $operator . ' ' . $value, 1, 0);

        $select->from(
            array('sales_order' => $this->getResource()->getTable('sales_order')),
            array(new \Zend_Db_Expr($result))
        );
        $this->_limitByStoreWebsite($select, $website, 'sales_order.store_id');
        $select->where($this->_createCustomerFilter($customer, 'sales_order.customer_id'));
        $select->limit(1);
        return $select;
    }

    /**
     * Reset setValueOption() to prevent displaying incorrect actual values
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array());
        return $this;
    }
}

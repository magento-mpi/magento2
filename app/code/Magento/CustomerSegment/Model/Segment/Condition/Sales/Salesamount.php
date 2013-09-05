<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Orders amount condition
 */
class Magento_CustomerSegment_Model_Segment_Condition_Sales_Salesamount
    extends Magento_CustomerSegment_Model_Segment_Condition_Sales_Combine
{
    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Sales_Salesamount');
        $this->setValue(null);
    }

    /**
     * Set data with filtering
     *
     * @param mixed $key
     * @param mixed $value
     * @return Magento_CustomerSegment_Model_Segment_Condition_Sales_Salesamount
     */
    public function setData($key, $value = null)
    {
        //filter key "value"
        if (is_array($key) && isset($key['value']) && $key['value'] !== null) {
            $key['value'] = (float)$key['value'];
        } elseif ($key == 'value' && $value !== null) {
            $value = (float)$value;
        }

        return parent::setData($key, $value);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
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
        return $this->getTypeElementHtml()
            . __('%1 Sales Amount %2 %3 while %4 of these Conditions match:', $this->getAttributeElementHtml(), $this->getOperatorElementHtml(), $this->getValueElementHtml(), $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Build query for matching orders amount
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @return \Magento\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();

        $operator = $this->getResource()->getSqlOperator($this->getOperator());
        $aggrFunc = ($this->getAttribute() == 'total') ? 'SUM' : 'AVG';
        $adapter = $this->getResource()->getReadConnection();
        $firstIf = $adapter->getCheckSql($aggrFunc . '(sales_order.base_grand_total) IS NOT NULL',
            $aggrFunc . '(sales_order.base_grand_total)', 0);
        $value = (float)$this->getValue();
        $result = $adapter->getCheckSql($firstIf . ' ' . $operator . ' ' . $value, 1, 0);

        $select->from(
            array('sales_order' => $this->getResource()->getTable('sales_flat_order')),
            array(new Zend_Db_Expr($result))
        );
        $this->_limitByStoreWebsite($select, $website, 'sales_order.store_id');
        $select->where($this->_createCustomerFilter($customer, 'sales_order.customer_id'));
        $select->limit(1);
        return $select;
    }

    /**
     * Reset setValueOption() to prevent displaying incorrect actual values
     *
     * @return Magento_CustomerSegment_Model_Segment_Condition_Sales_Salesamount
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array());
        return $this;
    }
}

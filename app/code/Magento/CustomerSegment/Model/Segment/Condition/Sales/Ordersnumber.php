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
 * Order numbers condition
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Sales;

class Ordersnumber
    extends \Magento\CustomerSegment\Model\Segment\Condition\Sales\Combine
{
    /**
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\Rule\Model\Condition\Context $context,
        array $data = array()
    ) {
        parent::__construct($resourceSegment, $conditionFactory, $context, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Sales\Ordersnumber');
        $this->setValue(null);
    }

    /**
     * Set data with filtering
     *
     * @param mixed $key
     * @param mixed $value
     * @return \Magento\CustomerSegment\Model\Segment\Condition\Sales\Ordersnumber
     */
    public function setData($key, $value = null)
    {
        //filter key "value"
        if (is_array($key) && isset($key['value']) && $key['value'] !== null) {
            $key['value'] = (int)$key['value'];
        } elseif ($key == 'value' && $value !== null) {
            $value = (int)$value;
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
     * Redeclare value options. We use empty because value is text input
     *
     * @return \Magento\CustomerSegment\Model\Segment\Condition\Sales\Ordersnumber
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array());
        return $this;
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Number of Orders %1 %2 while %3 of these Conditions match:', $this->getOperatorElementHtml(), $this->getValueElementHtml(), $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Build query for matching orders count
     *
     * @param $customer
     * @param int | \Zend_Db_Expr $website
     * @return \Magento\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $adapter = $this->getResource()->getReadConnection();
        $value = $adapter->quote($this->getValue());
        $result = $adapter->getCheckSql("COUNT(*) {$operator} {$value}", 1, 0);

        $select->from(
            array('sales_order' => $this->getResource()->getTable('sales_flat_order')),
            array(new \Zend_Db_Expr($result))
        );
        $this->_limitByStoreWebsite($select, $website, 'sales_order.store_id');
        $select->where($this->_createCustomerFilter($customer, 'sales_order.customer_id'));

        return $select;
    }
}

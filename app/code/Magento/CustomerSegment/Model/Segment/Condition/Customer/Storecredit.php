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
 * Customer store credit condition
 */
class Magento_CustomerSegment_Model_Segment_Condition_Customer_Storecredit
    extends Magento_CustomerSegment_Model_Condition_Abstract
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * @param Magento_CustomerSegment_Model_Resource_Segment $resourceSegment
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CustomerSegment_Model_Resource_Segment $resourceSegment,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        parent::__construct($resourceSegment, $context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Customer_Storecredit');
        $this->setValue(null);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array('customer_balance_save_commit_after');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(array(
            'value' => $this->getType(),
            'label' => __('Store Credit')
         ));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        $operator = $this->getOperatorElementHtml();
        $element = $this->getValueElementHtml();
        return $this->getTypeElementHtml() . __('Customer Store Credit Amount %1 %2:', $operator, $element)
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get condition query for customer balance on specific website
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @return Magento_DB_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('magento_customerbalance');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from($table, array(new Zend_Db_Expr(1)));
        $select->where($this->_createCustomerFilter($customer, 'customer_id'));
        $select->where('website_id=?', $website);
        $select->where("amount {$operator} ?", $this->getValue());
        $select->limit(1);
        return $select;
    }
}

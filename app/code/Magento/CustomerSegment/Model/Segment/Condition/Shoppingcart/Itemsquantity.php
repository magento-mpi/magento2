<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_CustomerSegment_Model_Segment_Condition_Shoppingcart_Itemsquantity
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
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Shoppingcart_Itemsquantity');
        $this->setValue(null);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array('sales_quote_save_commit_after');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(
            'value' => $this->getType(),
            'label' => __('Number of Cart Line Items'),
            'available_in_guest_mode' => true
        );
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Number of Shopping Cart Line Items %1 %2:', $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get SQL select for matching shopping cart items count
     *
     * @param $customer
     * @param int|Zend_Db_Expr $website
     * @return Magento_Db_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('sales_flat_quote');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('quote' => $table), array(new Zend_Db_Expr(1)))->where('quote.is_active=1');
        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->limit(1);
        $select->where("quote.items_count {$operator} ?", $this->getValue());
        if ($customer) {
            // Leave ability to check this condition not only by customer_id but also by quote_id
            $select->where('quote.customer_id = :customer_id OR quote.entity_id = :quote_id');
        } else {
            $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        }

        return $select;
    }
}

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
 * Order address condition
 */
class Magento_CustomerSegment_Model_Segment_Condition_Order_Address
    extends Magento_CustomerSegment_Model_Condition_Combine_Abstract
{
    /**
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @var Magento_CustomerSegment_Model_ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @param Magento_CustomerSegment_Model_ConditionFactory $conditionFactory
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CustomerSegment_Model_ConditionFactory $conditionFactory,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_conditionFactory = $conditionFactory;
        parent::__construct($context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Order_Address');
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
     * Get List of available selections inside this combine
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return $this->_conditionFactory
            ->create('Magento_CustomerSegment_Model_Segment_Condition_Order_Address_Combine')
            ->getNewChildSelectOptions();
    }

    /**
     * Get html of order address combine
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('If Order Addresses match %1 of these Conditions:', $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Order address combine doesn't have declared value. We use "1" for it
     *
     * @return int
     */
    public function getValue()
    {
        return 1;
    }

    /**
     * Prepare base condition select which related with current condition combine
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @return Magento_DB_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $resource = $this->getResource();
        $select = $resource->createSelect();

        $mainAddressTable   = $this->getResource()->getTable('sales_flat_order_address');
        $extraAddressTable  = $this->getResource()->getTable('magento_customer_sales_flat_order_address');
        $orderTable         = $this->getResource()->getTable('sales_flat_order');

        $select->from(array('order_address' => $mainAddressTable), array(new Zend_Db_Expr(1)))
            ->join(
                array('order_address_order' => $orderTable),
                'order_address.parent_id = order_address_order.entity_id',
                array()
            )
            ->joinLeft(
                array('extra_order_address' => $extraAddressTable),
                'order_address.entity_id = extra_order_address.entity_id',
                array()
            )
            ->where($this->_createCustomerFilter($customer, 'order_address_order.customer_id'));
        $select->limit(1);
        $this->_limitByStoreWebsite($select, $website, 'order_address_order.store_id');
        return $select;
    }

    /**
     * Order address is joined to base query. We are applying address type condition as subfilter for main query
     *
     * @return array
     */
    protected function _getSubfilterMap()
    {
        return array(
            'order_address_type' => 'order_address_type.value',
        );
    }
}

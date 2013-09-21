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
 * Customer address attributes conditions combine
 */
class Magento_CustomerSegment_Model_Segment_Condition_Customer_Address
    extends Magento_CustomerSegment_Model_Condition_Combine_Abstract
{
    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @var Magento_CustomerSegment_Model_ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @param Magento_CustomerSegment_Model_ConditionFactory $conditionFactory
     * @param Magento_CustomerSegment_Model_Resource_Segment $resourceSegment
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CustomerSegment_Model_ConditionFactory $conditionFactory,
        Magento_CustomerSegment_Model_Resource_Segment $resourceSegment,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_conditionFactory = $conditionFactory;
        $this->_eavConfig = $eavConfig;
        parent::__construct($resourceSegment, $context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Customer_Address');
    }

    /**
     * Get list of available sub-conditions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $result = array_merge_recursive(parent::getNewChildSelectOptions(), array(
            array(
                'value' => $this->getType(),
                'label' => __('Conditions Combination'),
            ),
            $this->_conditionFactory->create('Customer_Address_Default')->getNewChildSelectOptions(),
            $this->_conditionFactory->create('Customer_Address_Attributes')->getNewChildSelectOptions(),
        ));
        return $result;
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('If Customer Addresses match %1 of these Conditions:', $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Condition presented without value select. Default value is "1"
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
     * @param $website
     * @return Magento_DB_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $resource = $this->getResource();
        $select = $resource->createSelect();
        $addressEntityType = $this->_eavConfig->getEntityType('customer_address');
        $addressTable = $resource->getTable($addressEntityType->getEntityTable());
        $select->from(array('customer_address' => $addressTable), array(new Zend_Db_Expr(1)));
        $select->where('customer_address.entity_type_id = ?', $addressEntityType->getId());
        $select->where($this->_createCustomerFilter($customer, 'customer_address.parent_id'));
        $select->limit(1);
        return $select;
    }

}

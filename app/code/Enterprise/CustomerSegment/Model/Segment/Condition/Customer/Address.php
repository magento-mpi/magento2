<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer address attributes conditions combine
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Address
    extends Enterprise_CustomerSegment_Model_Condition_Combine_Abstract
{
    /**
     * @param Mage_Rule_Model_Condition_Context $context
     */
    public function __construct(Mage_Rule_Model_Condition_Context $context)
    {
        parent::__construct($context);
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Address');
    }

    /**
     * Get list of available sub-conditions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $prefix = 'Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Address_';
        $result = array_merge_recursive(parent::getNewChildSelectOptions(), array(
            array(
                'value' => $this->getType(),
                'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Conditions Combination')
            ),
            Mage::getModel($prefix.'Default')->getNewChildSelectOptions(),
            Mage::getModel($prefix.'Attributes')->getNewChildSelectOptions(),
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
            . Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('If Customer Addresses match %s of these Conditions:', $this->getAggregatorElement()->getHtml())
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
     * @return Varien_Db_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $resource = $this->getResource();
        $select = $resource->createSelect();
        $addressEntityType = Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType('customer_address');
        $addressTable = $resource->getTable($addressEntityType->getEntityTable());

        $select->from(array('customer_address' => $addressTable), array(new Zend_Db_Expr(1)));
        $select->where('customer_address.entity_type_id = ?', $addressEntityType->getId());
        $select->where($this->_createCustomerFilter($customer, 'customer_address.parent_id'));

        Mage::getResourceHelper('Enterprise_CustomerSegment')->setOneRowLimit($select);

        return $select;
    }

}

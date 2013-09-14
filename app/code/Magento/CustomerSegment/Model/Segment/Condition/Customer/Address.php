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
namespace Magento\CustomerSegment\Model\Segment\Condition\Customer;

class Address
    extends \Magento\CustomerSegment\Model\Condition\Combine\AbstractCombine
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Customer\Address');
    }

    /**
     * Get list of available sub-conditions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $prefix = 'Magento\CustomerSegment\Model\Segment\Condition\Customer\Address\\';
        $result = array_merge_recursive(parent::getNewChildSelectOptions(), array(
            array(
                'value' => $this->getType(),
                'label' => __('Conditions Combination')
            ),
            \Mage::getModel($prefix.'Default')->getNewChildSelectOptions(),
            \Mage::getModel($prefix.'Attributes')->getNewChildSelectOptions(),
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
     * @return \Magento\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $resource = $this->getResource();
        $select = $resource->createSelect();
        $addressEntityType = \Mage::getSingleton('Magento\Eav\Model\Config')->getEntityType('customer_address');
        $addressTable = $resource->getTable($addressEntityType->getEntityTable());
        $select->from(array('customer_address' => $addressTable), array(new \Zend_Db_Expr(1)));
        $select->where('customer_address.entity_type_id = ?', $addressEntityType->getId());
        $select->where($this->_createCustomerFilter($customer, 'customer_address.parent_id'));
        $select->limit(1);
        return $select;
    }

}

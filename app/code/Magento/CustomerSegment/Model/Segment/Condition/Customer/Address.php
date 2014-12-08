<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Customer;

use Magento\Customer\Model\Customer;
use Magento\CustomerSegment\Model\Condition\Combine\AbstractCombine;

/**
 * Customer address attributes conditions combine
 */
class Address extends AbstractCombine
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $conditionFactory, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Customer\Address');
    }

    /**
     * Get list of available sub-conditions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $result = array_merge_recursive(
            parent::getNewChildSelectOptions(),
            [
                ['value' => $this->getType(), 'label' => __('Conditions Combination')],
                $this->_conditionFactory->create('Customer\Address\DefaultAddress')->getNewChildSelectOptions(),
                $this->_conditionFactory->create('Customer\Address\Attributes')->getNewChildSelectOptions()
            ]
        );
        return $result;
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml() . __(
            'If Customer Addresses match %1 of these Conditions:',
            $this->getAggregatorElement()->getHtml()
        ) . $this->getRemoveLinkHtml();
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
     * @param Customer|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return \Magento\Framework\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $resource = $this->getResource();
        $select = $resource->createSelect();
        $addressEntityType = $this->_eavConfig->getEntityType('customer_address');
        $addressTable = $resource->getTable($addressEntityType->getEntityTable());
        $select->from(['customer_address' => $addressTable], [new \Zend_Db_Expr(1)]);
        $select->where('customer_address.entity_type_id = ?', $addressEntityType->getId());
        $select->where($this->_createCustomerFilter($customer, 'customer_address.parent_id'));
        $select->limit(1);
        return $select;
    }
}

<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Customer\Address;

use Magento\Customer\Model\Customer;
use Magento\CustomerSegment\Model\Condition\AbstractCondition;

/**
 * Customer address type selector
 */
class DefaultAddress extends AbstractCondition
{
    /**
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Customer\Address\DefaultAddress');
        $this->setValue('default_billing');
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        return [
            'customer_address_save_commit_after',
            'customer_save_commit_after',
            'customer_address_delete_commit_after'
        ];
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return ['value' => $this->getType(), 'label' => __('Default Address')];
    }

    /**
     * Init list of available values
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption(['default_billing' => __('Billing'), 'default_shipping' => __('Shipping')]);
        return $this;
    }

    /**
     * Get element type for value select
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml() . __(
            'Customer Address %1 Default %2 Address',
            $this->getOperatorElementHtml(),
            $this->getValueElement()->getHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Prepare is default billing/shipping condition for customer address
     *
     * @param Customer|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return \Magento\Framework\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $attribute = $this->_eavConfig->getAttribute('customer', $this->getValue());
        $select->from(['default' => $attribute->getBackendTable()], [new \Zend_Db_Expr(1)]);
        $select->where(
            'default.attribute_id = ?',
            $attribute->getId()
        )->where(
            'default.value=customer_address.entity_id'
        )->where(
            $this->_createCustomerFilter($customer, 'default.entity_id')
        );
        $select->limit(1);
        return $select;
    }
}

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
 * Customer address type selector
 */
class Magento_CustomerSegment_Model_Segment_Condition_Customer_Address_Default
    extends Magento_CustomerSegment_Model_Condition_Abstract
{
    /**
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Customer_Address_Default');
        $this->setValue('default_billing');
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array(
            'customer_address_save_commit_after',
            'customer_save_commit_after',
            'customer_address_delete_commit_after'
        );
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
            'label' => __('Default Address')
        );
    }

    /**
     * Init list of available values
     *
     * @return array
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            'default_billing'  => __('Billing'),
            'default_shipping' => __('Shipping'),
        ));
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
        return $this->getTypeElementHtml()
            . __('Customer Address %1 Default %2 Address', $this->getOperatorElementHtml(), $this->getValueElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Prepare is default billing/shipping condition for customer address
     *
     * @param $customer
     * @param $website
     * @return \Magento\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $attribute = Mage::getSingleton('Magento_Eav_Model_Config')->getAttribute('customer', $this->getValue());
        $select->from(array('default'=>$attribute->getBackendTable()), array(new Zend_Db_Expr(1)));
        $select->where('default.attribute_id = ?', $attribute->getId())
            ->where('default.value=customer_address.entity_id')
            ->where($this->_createCustomerFilter($customer, 'default.entity_id'));
        $select->limit(1);
        return $select;
    }
}

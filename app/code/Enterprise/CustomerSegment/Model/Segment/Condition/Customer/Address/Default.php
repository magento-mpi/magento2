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
 * Customer address type selector
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Address_Default
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    protected $_inputType = 'select';

    /**
     * Class constructor
     */
    public function __construct(Mage_Rule_Model_Condition_Context $context)
    {
        parent::__construct($context);
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Address_Default');
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
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Default Address')
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
            'default_billing'  => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Billing'),
            'default_shipping' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Shipping'),
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
            . Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Customer Address %s Default %s Address', $this->getOperatorElementHtml(), $this->getValueElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Prepare is default billing/shipping condition for customer address
     *
     * @param $customer
     * @param $website
     * @return Varien_Db_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $attribute = Mage::getSingleton('Mage_Eav_Model_Config')->getAttribute('customer', $this->getValue());
        $select->from(array('default'=>$attribute->getBackendTable()), array(new Zend_Db_Expr(1)));

        $select->where('default.attribute_id = ?', $attribute->getId())
            ->where('default.value=customer_address.entity_id')
            ->where($this->_createCustomerFilter($customer, 'default.entity_id'));

        Mage::getResourceHelper('Enterprise_CustomerSegment')->setOneRowLimit($select);

        return $select;
    }
}

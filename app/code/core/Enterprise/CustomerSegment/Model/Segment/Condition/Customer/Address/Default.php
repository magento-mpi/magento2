<?php
class Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Address_Default
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    protected $_inputType = 'select';

    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_customer_address_default');
        $this->setValue('primary_billing');
    }

    public function getNewChildSelectOptions()
    {
        return array(
            'value' => $this->getType(),
            'label' => Mage::helper('enterprise_customersegment')->__('Default Address')
        );
    }

    public function loadValueOptions()
    {
        $this->setValueOption(array(
            'primary_billing'  => Mage::helper('enterprise_customersegment')->__('Billing'),
            'primary_shipping' => Mage::helper('enterprise_customersegment')->__('Shipping'),
        ));
        return $this;
    }

    public function getValueElementType()
    {
        return 'select';
    }

    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_customersegment')->__('Customer Address %s Default %s Address',
                $this->getOperatorElementHtml(), $this->getValueElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }
}

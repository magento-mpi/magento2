<?php
class Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address_Type
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    protected $_inputType = 'select';

    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_order_address_type');
        $this->setValue('shipping');
    }

    public function getNewChildSelectOptions()
    {
        return array(
            'value' => $this->getType(),
            'label' => Mage::helper('enterprise_customersegment')->__('Address Type')
        );
    }

    public function loadValueOptions()
    {
        $this->setValueOption(array(
            'shipping' => Mage::helper('enterprise_customersegment')->__('Shipping'),
            'billing'  => Mage::helper('enterprise_customersegment')->__('Billing'),
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
            . Mage::helper('enterprise_customersegment')->__('Order Address %s a %s Address',
                $this->getOperatorElementHtml(), $this->getValueElement()->getHtml()) . $this->getRemoveLinkHtml();
    }

    public function getSubfilterType()
    {
        return 'order_address_type';
    }

    public function getSubfilterSql($fieldName, $requireValid)
    {
        $operator = (($this->getOperator() == '==') == $requireValid);
        if ($operator) {
            $operator = '=';
        } else {
            $operator = '<>';
        }

        return sprintf("%s %s '%s'", $fieldName, $operator, $this->getValue());
    }
}

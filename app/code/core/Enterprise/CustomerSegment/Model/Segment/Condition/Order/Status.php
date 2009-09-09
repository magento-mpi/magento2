<?php
class Enterprise_CustomerSegment_Model_Segment_Condition_Order_Status
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    protected $_inputType = 'select';

    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_order_status');
        $this->setValue(null);
    }

    public function getNewChildSelectOptions()
    {
        return array(
            'value' => $this->getType(),
            'label' => Mage::helper('enterprise_customersegment')->__('Order Status')
        );
    }

    public function getValueElementType()
    {
        return 'select';
    }

    public function loadValueOptions()
    {
        $this->setValueOption(array_merge(
            array('any' => Mage::helper('enterprise_customersegment')->__('Any')),
            Mage::getSingleton('sales/order_config')->getStatuses())
        );
        return $this;
    }

    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_customersegment')->__('Order Status %s %s:',
                $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }
}

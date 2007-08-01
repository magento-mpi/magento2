<?php

class Mage_Sales_Model_Order_Entity_Payment extends Mage_Customer_Model_Payment
{
    public function __construct($data=array())
    {
        parent::__construct($data);
        $this->setEntityType('payment');
    }
    
    public function onOrderValidate()
    {
        $modelConfig = Mage::getConfig()->getNode('global/sales/payment/methods/'.$this->getMethod());
        if (!$modelConfig) {
            return $this;
        }
        $modelClassName = $modelConfig->getClassName();
        $model = new $modelClassName();
        $model->onOrderValidate($this);
        
        return $this;
    }
}
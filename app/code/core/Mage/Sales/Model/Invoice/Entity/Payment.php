<?php

class Mage_Sales_Model_Invoice_Entity_Payment extends Mage_Customer_Model_Payment
{
    public function __construct($data=array())
    {
        parent::__construct($data);
        $this->setEntityType('payment');
    }
    
    public function onInvoiceCreate()
    {
        $modelConfig = Mage::getConfig()->getNode('global/salesPaymentMethods/'.$this->getMethod());
        if (!$modelConfig) {
            return $this;
        }
        $modelClassName = $modelConfig->getClassName();
        $model = new $modelClassName();
        $model->onInvoiceCreate($this);
        
        return $this;
    }
}
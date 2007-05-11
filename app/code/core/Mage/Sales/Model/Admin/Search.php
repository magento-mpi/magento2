<?php

class Mage_Sales_Model_Admin_Search extends Varien_Object 
{
    public function load()
    {
        $arr = array();
        
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        
        $collection = Mage::getModel('sales_resource', 'order_collection')
            ->addAttributeSelect('self/real_order_id')
            ->addAttributeSelect('address/address_type')
            ->addAttributeSelect('address/firstname')
            ->addAttributeSelect('address/lastname')
            ->addAttributeFilter('self/real_order_id', $this->getQuery(), 'or')
            ->addAttributeSelect('address/address_type', 'billing')
            ->addAttributeFilter('address/firstname', array('like'=>$this->getQuery().'%'), 'or')
            ->addAttributeFilter('address/lastname', array('like'=>$this->getQuery().'%'), 'or')
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->loadData();
        
        foreach ($collection as $order) {
            $billing = $order->getAddressByType('billing');
            $arr[] = array(
                'id'            => 'order/1/'.$order->getOrderId(),
                'type'          => 'Order',
                'name'          => 'Order # '.$order->getRealOrderId(),
                'description'   => $billing->getFirstname().' '.$billing->getLastname(),
                'form_panel_title' => 'Order # '.$order->getRealOrderId().' ('.$billing->getFirstname().' '.$billing->getLastname().')'
            );
        }
        
        $this->setResults($arr);
        
        return $this;
    }
}
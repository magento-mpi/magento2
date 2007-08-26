<?php

class Mage_Adminhtml_Model_Search_Order extends Varien_Object 
{
    public function load()
    {
        $arr = array();
        
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            
            ->joinAttribute('billing_firstname', 'order_address/firstname', 'billing_address_id')
            ->joinAttribute('billing_lastname', 'order_address/lastname', 'billing_address_id')
            ->joinAttribute('billing_telephone', 'order_address/telephone', 'billing_address_id')
            ->joinAttribute('billing_postcode', 'order_address/postcode', 'billing_address_id')
            
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id')
            ->joinAttribute('shipping_telephone', 'order_address/telephone', 'shipping_address_id')
            ->joinAttribute('shipping_postcode', 'order_address/postcode', 'shipping_address_id')
            
            ->addAttributeToFilter(array(
                array('attribute'=>'billing_firstname', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'billing_lastname', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'billing_telephone', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'billing_postcode', 'like'=>$this->getQuery().'%'),
                
                array('attribute'=>'shipping_firstname', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'shipping_lastname', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'shipping_telephone', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'shipping_postcode', 'like'=>$this->getQuery().'%'),
            ))
            
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        
        foreach ($collection as $order) {
            $arr[] = array(
                'id'            => 'order/1/'.$order->getId(),
                'type'          => 'Order',
                'name'          => 'Order # '.$order->getIncrementId(),
                'description'   => $order->getBillingFirstname().' '.$order->getBillingLastname(),
                'form_panel_title' => 'Order # '.$order->getIncrementId().' ('.$order->getBillingFirstname().' '.$order->getBillingLastname().')',
                'url'           => Mage::getUrl('*/sales_order/edit', array('order_id'=>$order->getId())),
            );
        }
        
        $this->setResults($arr);
        
        return $this;
    }
}
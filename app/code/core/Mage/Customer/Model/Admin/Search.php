<?php

class Mage_Customer_Model_Admin_Search extends Varien_Object 
{
    public function load()
    {
        $arr = array();
        
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addFilter('firstname', $this->getQuery(), 'or')
            ->addFilter('lastname', $this->getQuery(), 'or')
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->loadData();
        
        foreach ($collection as $customer) {
            $arr[] = array(
                'id'            => 'customer/1/'.$customer->getCustomerId(),
                'type'          => 'Customer',
                'name'          => $customer->getFirstname().' '.$customer->getLastname(),
                'description'   => 'No description',
            );
        }
        
        $this->setResults($arr);
        
        return $this;
    }
}
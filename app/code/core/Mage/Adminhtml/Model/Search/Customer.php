<?php

class Mage_Adminhtml_Model_Search_Customer extends Varien_Object 
{
    public function load()
    {
        $arr = array();
        
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToFilter(array(
                array('attribute'=>'firstname', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'lastname', 'like'=>$this->getQuery().'%')
            ))
            ->setPage(1, 10)
            ->load();
        
        foreach ($collection->getItems() as $customer) {
            $arr[] = array(
                'id'            => 'customer/1/'.$customer->getId(),
                'type'          => 'Customer',
                'name'          => $customer->getFirstname().' '.$customer->getLastname(),
                'description'   => 'No description',
                'url'           => Mage::getUrl('*/customer/edit', array('customer_id'=>$customer->getId())),
            );
        }
        
        $this->setResults($arr);
        
        return $this;
    }
}
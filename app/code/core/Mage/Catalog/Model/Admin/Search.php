<?php

class Mage_Catalog_Model_Admin_Search extends Varien_Data_Object 
{
    public function load()
    {
        $arr = array();
        
        if (!$this->getStart() || !$this->getLimit() || !$this->getQuery()) {
            return $arr;
        }
        
        $collection = Mage::getModel('catalog_resource', 'product_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description')
            ->addSearchFilter($this->getQuery())
            ->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->loadData();
        
        foreach ($collection as $product) {
            $arr[] = array(
                'id'            => $product->getProductId(),
                'type'          => 'product',
                'name'          => $product->getName(),
                'description'   => $product->getDescription(),
            );
        }
        
        if (empty($arr)) {
            $arr = array(
                'id'=>'error', 
                'type'=>'Error', 
                'name'=>'No search modules registered', 
                'description'=>'Please make sure that all global admin search modules are installed and activated.'
            );
        }
        
        return $arr;
    }
}
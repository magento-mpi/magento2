<?php

class Mage_Adminhtml_Model_Search_Catalog extends Varien_Object 
{
    public function load()
    {
        $arr = array();
        
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('description')
            ->addSearchFilter($this->getQuery())
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        
        foreach ($collection as $product) {
            $arr[] = array(
                'id'            => 'product/1/'.$product->getId(),
                'type'          => 'Product',
                'name'          => $product->getName(),
                'description'   => substr($product->getDescription(), 0, 50),
                'url'           => Mage::getUrl('adminhtml/catalog_product/edit', array('id'=>$product->getId())),
            );
        }
        
        $this->setResults($arr);
        
        return $this;
    }
}
<?php

class Mage_Catalog_Import {
    
    protected $_data = array();
    protected $_products = null;
    
    public function __construct()
    {
        $this->_products = Mage::getModel('catalog_resource', 'product_collection');
    }
    
    public function loadCsv($fileName, $fieldMap)
    {
        $this->_rows = array();
        $fp = fopen($fileName, 'r');
        while ($row = fgetcsv($fp, 0, "\t", '"')) {
            for ($i=0, $l=sizeof($row); $i<$l; $i++) {
                $data[$fieldMap[$i]] = $row[$i];
            }
            $this->_data[] = $data;
        }
        fclose($fp);
        return $this;
    }
    
    public function convert()
    {
        foreach ($this->_data as $data) {
            $product = $this->importProduct($data);
            if ($product) {
                $this->_products->addItem($product);
            }
        }
        return $this;
    }
    
    public function save()
    {
        print_r($this->_products);
        #$this->_products->walk('save');
        return $this;
    }
    
    public function importProduct($data)
    {
        $product = Mage::getModel('catalog', 'product');
        
        $product->addData($data);
        
        return $product;
    }
}
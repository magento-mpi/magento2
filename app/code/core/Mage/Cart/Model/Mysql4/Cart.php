<?php

class Mage_Cart_Model_Mysql4_Cart extends Mage_Cart_Model_Mysql4
{
    
    private $_dbModel = null;

    
    
    function __construct() {
        parent::__construct();
        $this->_dbModel = Mage::getModel('cart');
    }
    
    function getProducts($cartId=null)
    {
        $customer_Id = 1;
        $cart_Id = $this->getCustomerCart($customer_Id);
        $sql = $this->_read->select()
            ->from('cart_product')
            ->where('cart_id = ?', $cart_Id);
            
        $arr = $this->_read->fetchAll($sql);
        
        foreach($arr as $cartItem) {
            $pids[]= $cartItem['product_id'];
        }
        
        $ids = implode(",", $pids);
        $products = Mage::getModel('catalog', 'product_collection');
        $products->addAttributeToSelect('name', 'varchar');
        $products->addAttributeToSelect('price', 'decimal');
        $products->addFilter('id', 'catalog_product.product_id in ('.$ids.')', 'string');
        $products->load(true);
        
        
        foreach($products as $product) {
            Zend_Debug::dump($product);
        }
        
        echo $ids;
//        $arr = array(
//            array('id'=>1, 'qty'=>2, 'name'=>'Test Product', 'price'=>12.34),
//        );
        
        return $arr;
    }
    
    function getCustomerCart($customer_Id) {
        $sql = $this->_read->select()
            ->from('cart', array('cart_id'))
            ->where('customer_id = ?', $customer_Id);
        $cart_Id = $this->_read->fetchOne($sql);
        return $cart_Id;     
    }
    
    function createCart($customer_Id) {
        $this->_write->insert('cart', array(
            'cart_id' => 0,
            'customer_id' => $customer_Id,
            'create_date' => new Zend_Db_Expr('NOW()'),
            'uniq_code' => time()
        ));
        $cart_Id = $this->_write->lastInsertId();
        return $cart_Id;
    }
    
    function addProduct($product_Id)
    {
        $customer_Id = 1;
        $cart_Id = $this->getCustomerCart($customer_Id);
        if (!$cart_Id) {
            $cart_Id = $this->createCart($customer_Id);
        }
        
        $sql = $this->_read->select()
            ->from('cart_product', array('cart_id'))
            ->where('product_id = ?', $product_Id)
            ->where('cart_id = ?', $cart_Id);

        try {
            if ($this->_read->fetchRow($sql)) {
                $this->_write->update('cart_product', array(
                    'product_qty' => new Zend_Db_Expr('product_qty+1'),
                    ), $this->_write->quoteInto('cart_id = ?', $cart_Id) . ' AND ' . $this->_write->quoteInto('product_id = ?', $product_Id));
            } else {
                $this->_write->insert('cart_product', array(
                    'cart_product_id' => 0,
                    'cart_id' => $cart_Id,
                    'product_id' => $product_Id,
                    'product_qty' => 1,
                ));
            }
        } catch(PDOException $e) {
            return false;
        }
        return true;
    }
    
    function update($cartData, $cartId=null)
    {
        
    }
}
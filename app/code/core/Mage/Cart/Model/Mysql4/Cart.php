<?php

class Mage_Cart_Model_Mysql4_Cart extends Mage_Cart_Model_Mysql4
{
    
    function getItems($cart_Id=null)
    { 
        if (is_null($cart_Id)) {
            $cart_Id = $this->getCustomerCartId();
        }
        
        $sql = $this->_read->select()
            ->from($this->_getTableName('cart_setup', 'item'))
            ->where('cart_id = ?', $cart_Id);
            
        $arr = $this->_read->fetchAll($sql);
        
        $pids = array();
        foreach($arr as $cartItem) {
            $pids[]= $cartItem['product_id'];
        }
        
        $data = array();
        if (!empty($pids)) {
            $ids = implode(",", $pids);
            $products = Mage::getModel('catalog', 'product_collection');
            $products->setPageSize(false);
            $products->addAttributeToSelect('name', 'varchar');
            $products->addAttributeToSelect('price', 'decimal');
            $products->addFilter('id', 'catalog_product.product_id in ('.$ids.')', 'string');
            $products->load();
        
            foreach($arr as $cartItem) {
                $product = $products->getItemById($cartItem['product_id']);
                $price = $product->getTierPrice();
                $data[] = array(
                    'id' => $cartItem['product_id'],
                    'qty' => $cartItem['product_qty'],
                    'name' => $product->getName(),
                    'item_price' => $price,
                    'row_total' => $price * $cartItem['product_qty'],
                );
            }
        }
        return $data;
    }
    
    function getCustomerCartId() 
    {
        if (isset(Mage::registry('AUTH')->customer)) {
            $customer_Id = Mage::registry('AUTH')->customer->customer_id;
            $sql = $this->_read->select()
                ->from('cart', array('cart_id'))
                ->where('customer_id = ?', $customer_Id);
            $cart_Id = $this->_read->fetchOne($sql);
        } elseif(isset($_COOKIE['cart_uniq_code'])) {
            $sql = $this->_read->select()
                ->from('cart', array('cart_id'))
                ->where('uniq_code = ?', $_COOKIE['cart_uniq_code']);
            $cart_Id = $this->_read->fetchOne($sql);
        } else {
            return false;
        }
        return $cart_Id;     
    }
    
    function createCart() 
    {
        
        if (isset(Mage::registry('AUTH')->customer)) {
            $customer_Id = Mage::registry('AUTH')->customer->customer_id;
            $this->_write->insert('cart', array(
                'cart_id' => 0,
                'customer_id' => $customer_Id,
                'create_date' => new Zend_Db_Expr('NOW()')
            ));
        } else {
            $token = md5(uniqid(rand(), true));
            $this->_write->insert('cart', array(
                'cart_id' => 0,
                'create_date' => new Zend_Db_Expr('NOW()'),
                'uniq_code' => $token
            ));
            setcookie("cart_uniq_code", $token, time()+31104000, '/');
        }

        $cart_Id = $this->_write->lastInsertId();
        return $cart_Id;
    }
    
    function addProduct($product_Id)
    {
        $cart_Id = $this->getCustomerCartId();
        if (!$cart_Id) {
            $cart_Id = $this->createCart();
            if (!$cart_Id) {
                return false;
            }
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
    
    function update($cartData, $cart_Id=null)
    {
        if (empty($cart_Id)) {
            $cart_Id = $this->getCustomerCartId();
            if (!$cart_Id) {
                return false;
            }
        }
        
        foreach($cartData as $product_Id=>$prop) {
            if (isset($prop['remove']) && $prop['remove'] == 1) {
                try {
                $this->_write->delete('cart_product', 
                    $this->_write->quoteInto('cart_id = ?', $cart_Id) . ' AND ' . $this->_write->quoteInto('product_id = ?', $product_Id)
                );
                }catch(PDOException $e) {
                    var_dump($prop);
                }
            } else {
                $this->_write->update('cart_product', array(
                    'product_qty' => $prop['qty'],
                ), $this->_write->quoteInto('cart_id = ?', $cart_Id) . ' AND ' . $this->_write->quoteInto('product_id = ?', $product_Id));
            }
        }
    }
}
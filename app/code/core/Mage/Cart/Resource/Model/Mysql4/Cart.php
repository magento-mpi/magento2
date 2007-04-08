<?php

class Mage_Cart_Resource_Model_Mysql4_Cart extends Mage_Cart_Resource_Model_Mysql4
{

    function getCart($cartId=null)
    {
        $cartTable = Mage::registry('resources')->getTableName('cart', 'cart');
        
        if (is_null($cartId)) {
            $cartId = $this->getCustomerCartId();
        }
        
        $sql = $this->_read->select()->from($cartTable)->where('cart_id=?', $cartId);
        $arr = $this->_read->fetchRow($sql);
        
        return $arr;
    }
    
    function getItems($cartId=null)
    { 
        $cartTable = Mage::registry('resources')->getTableName('cart', 'cart');
        $itemTable = Mage::registry('resources')->getTableName('cart', 'item');
        $productTable = Mage::registry('resources')->getTableName('catalog', 'product');
        
        if (is_null($cartId)) {
            $cartId = $this->getCustomerCartId();
        }
        
        $sql = $this->_read->select()
            ->from($itemTable)
            ->where('cart_id = ?', $cartId);
            
        $arr = $this->_read->fetchAll($sql);
        
        $pids = array();
        foreach($arr as $cartItem) {
            $pids[]= $cartItem['product_id'];
        }
        
        $data = array();
        if (!empty($pids)) {
            $ids = implode(",", $pids);
            $products = Mage::getResourceModel('catalog', 'product_collection');
            $products->setPageSize(false);
            $products->addAttributeToSelect('name', 'varchar');
            $products->addAttributeToSelect('price', 'decimal');
            $products->addAttributeToSelect('weight', 'decimal');
            $products->addFilter('id', "$productTable.product_id in ($ids)", 'string');
            $products->load();
        
            foreach($arr as $cartItem) {
                $product = $products->getItemById($cartItem['product_id']);
                if ($product) {
                    $price = $product->getTierPrice($cartItem['product_qty']);
                    $data[] = array(
                        'id' => $cartItem['product_id'],
                        'qty' => $cartItem['product_qty'],
                        'name' => $product->getName(),
                        'weight' => $product->getWeight(),
                        'item_price' => $price,
                        'row_total' => $price * $cartItem['product_qty'],
                    );
                }
            }
        }
        
        return $data;
    }
    
    function getCustomerCartId() 
    {
        $cartTable = Mage::registry('resources')->getTableName('cart', 'cart');
        
        if ($customerId = Mage_Customer_Front::getCustomerId()) {
            $sql = $this->_read->select()
                ->from($cartTable, array('cart_id'))
                ->where('customer_id = ?', $customerId);
            $cartId = $this->_read->fetchOne($sql);
        } elseif(isset($_COOKIE['cart_uniq_code'])) {
            $sql = $this->_read->select()
                ->from($cartTable, array('cart_id'))
                ->where('uniq_code = ?', $_COOKIE['cart_uniq_code']);
            $cartId = $this->_read->fetchOne($sql);
        } else {
            return false;
        }
        return $cartId;     
    }
    
    function createCart() 
    {
        $cartTable = Mage::registry('resources')->getTableName('cart', 'cart');
        
        $cartData = array();
        if (isset(Mage::registry('AUTH')->customer)) {
            $cartData['customer_id'] = Mage::registry('AUTH')->customer->customer_id;
        } else {
            $token = md5(uniqid(rand(), true));
            $cartData['uniq_code'] = $token;
            setcookie("cart_uniq_code", $token, time()+31104000, '/');
        }
        $cartData['create_date'] = new Zend_Db_Expr('NOW()');
        $cartData['update_date'] = new Zend_Db_Expr('NOW()');
        $this->_write->insert($cartTable, $cartData);
        $cartId = $this->_write->lastInsertId();
        
        return $cartId;
    }
    
    function addProduct($productId, $qty=1)
    {
        $cartTable = Mage::registry('resources')->getTableName('cart', 'cart');
        $itemTable = Mage::registry('resources')->getTableName('cart', 'item');

        $cartId = $this->getCustomerCartId();
        if (!$cartId) {
            $cartId = $this->createCart();
            if (!$cartId) {
                return false;
            }
        }
        
        $sql = $this->_read->select()
            ->from($itemTable, array('cart_id'))
            ->where('product_id = ?', $productId)
            ->where('cart_id = ?', $cartId);

        try {
            $this->_write->update($cartTable, 
                array('update_date'=>new Zend_Db_Expr('NOW()')), 
                $this->_write->quoteInto('cart_id = ?', $cartId));
            
            if ($this->_read->fetchRow($sql)) {
                $this->_write->update($itemTable, array(
                    'product_qty' => new Zend_Db_Expr($this->_read->quoteInto('product_qty+?', $qty)),
                    ), $this->_write->quoteInto('cart_id = ?', $cartId) . ' AND ' . $this->_write->quoteInto('product_id = ?', $productId));
            } else {
                $this->_write->insert($itemTable, array(
                    'cart_id' => $cartId,
                    'product_id' => $productId,
                    'product_qty' => $qty,
                ));
            }
        } catch(PDOException $e) {
            return false;
        }
        return true;
    }
    
    function update($cartData, $cartId=null)
    {
        $cartTable = Mage::registry('resources')->getTableName('cart', 'cart');
        $itemTable = Mage::registry('resources')->getTableName('cart', 'item');

        if (empty($cartId)) {
            $cartId = $this->getCustomerCartId();
            if (!$cartId) {
                return false;
            }
        }
        
        $this->_write->update($cartTable,
            array('update_date'=>new Zend_Db_Expr('NOW()')),
            $this->_write->quoteInto('cart_id = ?', $cartId));

        foreach($cartData as $productId=>$prop) {
            if (isset($prop['remove']) && $prop['remove'] == 1) {
                try {
                    $this->_write->delete($itemTable,
                    $this->_write->quoteInto('cart_id = ?', $cartId) . ' AND ' . $this->_write->quoteInto('product_id = ?', $productId)
                );
                }catch(PDOException $e) {
                    var_dump($prop);
                }
            } else {
                $this->_write->update($itemTable, array(
                    'product_qty' => $prop['qty'],
                ), $this->_write->quoteInto('cart_id = ?', $cartId) . ' AND ' . $this->_write->quoteInto('product_id = ?', $productId));
            }
        }
    }
}
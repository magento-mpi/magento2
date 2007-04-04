<?php

class Mage_Cart_Resource_Model_Mysql4_Cart extends Mage_Cart_Resource_Model_Mysql4
{
    protected $_itemsCache = null;
    
    function cleanCache($cartId=null)
    {
        if ('*'===$cartId) {
            $this->_itemsCache = null;
            return;
        }
        
        if (is_null($cartId)) {
            $cartId = $this->getCustomerCartId();
        }
        
        $this->_itemsCache[$cartId] = null;
    }
    
    function getItems($cartId=null)
    { 
        if (is_null($cartId)) {
            $cartId = $this->getCustomerCartId();
        }
        
        if (!empty($this->_itemsCache[$cartId])) {
            return $this->_itemsCache[$cartId];
        }
        
        $sql = $this->_read->select()
            ->from($this->_getTableName('cart_setup', 'item'))
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
            $products->addFilter('id', 'catalog_product.product_id in ('.$ids.')', 'string');
            $products->load();
        
            foreach($arr as $cartItem) {
                $product = $products->getItemById($cartItem['product_id']);
                if ($product) {
                    $price = $product->getTierPrice($cartItem['product_qty']);
                    $data[] = array(
                        'id' => $cartItem['product_id'],
                        'qty' => $cartItem['product_qty'],
                        'name' => $product->getName(),
                        'item_price' => $price,
                        'row_total' => $price * $cartItem['product_qty'],
                    );
                }
            }
        }
        
        $this->_itemsCache[$cartId] = $data;
        
        return $data;
    }
    
    function getCustomerCartId() 
    {
        if ($customerId = Mage_Customer_Front::getCustomerId()) {
            $sql = $this->_read->select()
                ->from('cart', array('cart_id'))
                ->where('customer_id = ?', $customerId);
            $cartId = $this->_read->fetchOne($sql);
        } elseif(isset($_COOKIE['cart_uniq_code'])) {
            $sql = $this->_read->select()
                ->from('cart', array('cart_id'))
                ->where('uniq_code = ?', $_COOKIE['cart_uniq_code']);
            $cartId = $this->_read->fetchOne($sql);
        } else {
            return false;
        }
        return $cartId;     
    }
    
    function createCart() 
    {
        
        if (isset(Mage::registry('AUTH')->customer)) {
            $customerId = Mage::registry('AUTH')->customer->customer_id;
            $this->_write->insert('cart', array(
                'cart_id' => 0,
                'customer_id' => $customerId,
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

        $cartId = $this->_write->lastInsertId();
        return $cartId;
    }
    
    function addProduct($productId, $qty=1)
    {
        $cartId = $this->getCustomerCartId();
        if (!$cartId) {
            $cartId = $this->createCart();
            if (!$cartId) {
                return false;
            }
        }
        
        $sql = $this->_read->select()
            ->from('cart_product', array('cart_id'))
            ->where('product_id = ?', $productId)
            ->where('cart_id = ?', $cartId);

        try {
            if ($this->_read->fetchRow($sql)) {
                $this->_write->update('cart_product', array(
                    'product_qty' => new Zend_Db_Expr($this->_read->quoteInto('product_qty+?', $qty)),
                    ), $this->_write->quoteInto('cart_id = ?', $cartId) . ' AND ' . $this->_write->quoteInto('product_id = ?', $productId));
            } else {
                $this->_write->insert('cart_product', array(
                    'cart_product_id' => 0,
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
        if (empty($cartId)) {
            $cartId = $this->getCustomerCartId();
            if (!$cartId) {
                return false;
            }
        }
        
        foreach($cartData as $productId=>$prop) {
            if (isset($prop['remove']) && $prop['remove'] == 1) {
                try {
                $this->_write->delete('cart_product', 
                    $this->_write->quoteInto('cart_id = ?', $cartId) . ' AND ' . $this->_write->quoteInto('product_id = ?', $productId)
                );
                }catch(PDOException $e) {
                    var_dump($prop);
                }
            } else {
                $this->_write->update('cart_product', array(
                    'product_qty' => $prop['qty'],
                ), $this->_write->quoteInto('cart_id = ?', $cartId) . ' AND ' . $this->_write->quoteInto('product_id = ?', $productId));
            }
        }
    }
}
<?php
/**
 * Customer wishlist mysql4 resource
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Wishlist
{
    protected $_read;
    protected $_write;

    protected $_wishlistTable;
    
    public function __construct()
    {
        $this->_wishlistTable = Mage::registry('resources')->getTableName('customer_resource', 'wishlist');
        $this->_read = Mage::registry('resources')->getConnection('customer_read');
        $this->_write = Mage::registry('resources')->getConnection('customer_write');
    }

    /**
     * Get wishlist data
     *
     * @param   int $itemId
     * @param   string || array $fields
     * @return  array
     */
    public function load($itemId)
    {
        $sql = "SELECT * FROM $this->_wishlistTable WHERE item_id=:item_id";
        $arr = $this->_read->fetchRow($sql, array('item_id'=>$itemId));
        return $arr;
    }
    
    public function loadByCustomerProduct($customerId, $productId) 
    {
        $sql = "SELECT * FROM $this->_wishlistTable WHERE customer_id=:customer_id and product_id=:product_id";
        $arr = $this->_read->fetchRow($sql, array('customer_id'=>$customerId, 'product_id'=>$productId));
        return $arr;
    }

    public function save(Mage_Customer_Model_Wishlist $wishlist)
    {
        $this->_write->beginTransaction();
        try {
            $data = array(
                'product_id'  => $wishlist->getProductId(),
                'customer_id' => $wishlist->getCustomerId(),
                'uniq_code'   => $wishlist->getUniqCode()
            );
            if ($wishlist->getId()) {
                $condition = $this->_write->quoteInto('item_id=?', $wishlist->getId());
                $this->_write->update($this->_wishlistTable, $data, $condition);
            }
            else {
                $this->_write->insert($this->_wishlistTable, $data);
                $wishlist->setItemId($this->_write->lastInsertId());
            }
            
            $this->_write->commit();
        }
        catch (Exception $e){
            $this->_write->rollBack();
            throw $e;
        }
    }
    
    public function delete($wishlistId)
    {
        $condition = $this->_write->quoteInto('item_id=?', $wishlistId);
        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->_wishlistTable, $condition);
            $this->_write->commit();
        }
        catch (Exception $e)
        {
            $this->_write->rollBack();
            throw $e;
        }
    }
}
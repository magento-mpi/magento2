<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer wishlist mysql4 resource
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Mysql4_Wishlist
{
    protected $_read;
    protected $_write;

    protected $_wishlistTable;
    
    public function __construct()
    {
        $this->_wishlistTable = Mage::getSingleton('core/resource')->getTableName('customer/wishlist');
        $this->_read = Mage::getSingleton('core/resource')->getConnection('customer_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('customer_write');
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
                'add_date'    => $wishlist->getAddDate(),
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
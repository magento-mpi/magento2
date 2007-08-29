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
 * Wishlist mysql4 collection model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Mysql4_Wishlist_Collection extends Varien_Data_Collection_Db
{
    protected $_wishlistTable;
    protected $_productCollection;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('catalog_read'));
        
        $this->_wishlistTable = Mage::getSingleton('core/resource')->getTableName('customer/wishlist');
        $this->_productCollection = Mage::getResourceModel('catalog/product_collection');
        $this->_sqlSelect->from($this->_wishlistTable);
    }
    
    public function getProductCollection()
    {
        return $this->_productCollection;
    }

    public function addCustomerFilter($condition)
    {
        $this->_sqlSelect->where($this->_getConditionSql("$this->_wishlistTable.customer_id", $condition));
        return $this;
    }   
    
    protected function _loadLinkedProducts()
    {
        $arrProductId = $this->getColumnValues('product_id');
        if (empty($arrProductId)) {
            return false;
        }
        $this->getProductCollection()->addProductFilter(array('in'=>$arrProductId));
        $linkedProducts = $this->getProductCollection()->loadData();
            
        foreach ($this->getItems() as $item) {
            $item->setProduct($linkedProducts->getItemById($item->getProductId()));
        }
        return true;
    }
    
    public function loadData($printQuery=false, $logQuery=false)
    {
        if (!parent::loadData($printQuery, $logQuery)) {
            return $this;
        }
        
        $this->_loadLinkedProducts();        
        return $this;
    }
}
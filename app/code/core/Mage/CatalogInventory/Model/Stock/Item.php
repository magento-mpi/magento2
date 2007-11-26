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
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Stock item model
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_CatalogInventory_Model_Stock_Item extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloginventory/stock_item');
    }
    
    /**
     * Retrieve stock identifier
     *
     * @return int
     */
    public function getStockId()
    {
        return 1;
    }
    
    /**
     * Adding stoc data to product
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_CatalogInventory_Model_Stock_Item
     */
    public function assignProduct(Mage_Catalog_Model_Product $product)
    {
        $this->getResource()->loadByProduct($this, $product);
        $product->setStockItem($this);
        
        $this->setProduct($product);
        return $this;
    }
    
    /**
     * Retrieve minimal quantity available for item status in stock
     *
     * @return decimal
     */
    public function getMinQty()
    {
        return $this->getData('min_qty');
    }
    
    /**
     * Retrieve backorders status
     *
     * @return int
     */
    public function getBackorders()
    {
        return $this->getData('backorders');
    }
}
<?php
/**
 * Product compare list
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Product_Compare_List extends Varien_Object 
{
    public function addProduct($product)
    {
        $item = Mage::getModel('catalog/product_compare_item');
        $this->_addVisitorToItem($item);
		$item->loadByProduct($product);

		if(!$item->getId()) {
			$item->addProductData($product);
			$item->save();
		}
        return $this;
    }
    
    public function addProducts($productIds)
    {
        if (is_array($productIds)) {
            foreach ($productIds as $productId) {
            	$this->addProduct($productId);
            }
        }
        return $this;
    }
    
    public function getItems()
    {
        
    }
    
    public function removeProduct()
    {
        
    }
    
    protected function _addVisitorToItem($item)
    {
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
		} else {
			$item->addVisitorId(Mage::getSingleton('core/session')->getLogVisitorId());
		}
		return $this;
    }
}

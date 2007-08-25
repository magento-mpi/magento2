<?php
/**
 * Cart crosssell list
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Block_Cart_Crosssell extends Mage_Catalog_Block_Product_Abstract
{
    protected $_maxItemCount = 3;
    
    public function getItems()
    {
        $items = $this->getData('items');
        if (is_null($items)) {
            $items = array();
            
            if ($lastAdded = (int) $this->_getLastAddedProductId()) {
                $collection = $this->_getCollection()
                    ->addFieldToFilter('product_id', $lastAdded)
                    ->load();
                /*echo $collection->getSelect();
                var_dump($collection->getSize());*/
            }
            
            $this->setData('items', $items);
        }
        return $items;
    }
    
    public function getItemCount()
    {
        return count($this->getItems());
    }
    
    protected function _getCartProductIds()
    {
        $ids = array();
        foreach ($this->getQuote()->getAllItems() as $item) {
        	$ids[] = $item->getProductId();
        }
        return $ids;
    }
    
    protected function _getLastAddedProductId()
    {
        return Mage::getSingleton('checkout/session')->getLastAddedProductId(true);
    }
    
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }
    
    protected function _getCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_link_collection')
            ->joinLinkTable()
			->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('thumbnail')
            ->setLinkType('cross_sell')
            ->setStoreId(Mage::getSingleton('core/store')->getId())
            ->addLinkTypeFilter()
            ->addStoreFilter();
        $ninProductIds = $this->_getCartProductIds();
        if (!empty($ninProductIds)) {
            $collection->addFieldToFilter('product_id', array('nin'=>$ninProductIds));
        }
        return $collection;
    }
}

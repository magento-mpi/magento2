<?php
/**
 * Product Stores tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Stores extends Mage_Adminhtml_Block_Store_Switcher
{
    protected $_storeCillection;
    protected $_storeFromHtml;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/stores.phtml');
        $this->_storeCillection = Mage::getResourceModel('core/store_collection')
            ->load();
    }
    
    public function getStoreId()
    {
        return Mage::registry('product')->getStoreId();
    }
    
    public function getProductId()
    {
        return Mage::registry('product')->getId();
    }
    
    public function isProductInStore($storeId)
    {
        return in_array($storeId, Mage::registry('product')->getStoreIds());
    }
    
    public function getStoreName($storeId)
    {
        if ($store = $this->_storeCillection->getItemById($storeId)) {
            return $store->getName();
        }
        return '';
    }
    
    public function getStoreCollection()
    {
        return $this->_storeCillection;
    }
    
    public function getChooseFromStoreHtml()
    {
        if (!$this->_storeFromHtml) {
            $stores = Mage::registry('product')->getStoreIds();
            $this->_storeFromHtml = '<select name="store_chooser">';
            $this->_storeFromHtml.= '<option value="0">'.__('Default Store').'</option>';
            foreach ($this->_storeCillection as $store) {
            	if ($store->getId() && in_array($store->getId(), $stores)) {
            	    $this->_storeFromHtml.= '<option value="'.$store->getId().'">'.$store->getName().'</option>';
            	}
            }
            $this->_storeFromHtml.= '</select>';
        }
        return $this->_storeFromHtml;
    }
}

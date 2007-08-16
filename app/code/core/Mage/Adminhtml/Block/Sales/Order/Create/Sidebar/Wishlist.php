<?php
/**
 * Adminhtml sales order create sidebar wishlist block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Wishlist extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_sidebar_wishlist');
    }

    protected function _prepareItems()
    {
        $this->setItems( Mage::getModel('wishlist/wishlist')->loadByCustomer($this->getSession()->getCustomer())->getItemCollection()
        	->addAttributeToSelect('name')
        	->addAttributeToSelect('price')
        	->addAttributeToSelect('small_image')
        	->addStoreData()
        	->load()
    	);
        return $this;
    }

    public function hasItems()
    {
        if (Mage::getSingleton('adminhtml/quote')->getCustomer()) {
            return parent::hasItems();
        }
        return false;
    }

    public function getHeaderText()
    {
        return __('Wishlist');
    }

}

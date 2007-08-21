<?php
/**
 * Adminhtml wishlist report page content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */

class Mage_Adminhtml_Block_Report_Wishlist extends Mage_Core_Block_Template
{
    public $wishlists_count;
    public $items_bought;
    public $shared_count;
    public $referrals_count;
    public $conversions_count;
    public $customer_with_wishlist;
    
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('report/wishlist.phtml');
    }

    public function _beforeToHtml()
    {      
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/report_wishlist_grid', 'report.grid'));
            
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->getSelect()->from("wishlist", array("count(wishlist_id) as wishlist_cnt"))
                    ->where("wishlist.customer_id=e.entity_id")
                    ->group("wishlist.customer_id");
                    
        $collection->load();
        
        $this->customer_with_wishlist = (int)$collection->getSize();
        
        $this->wishlists_count = 0;
        $this->items_bought = 0;
        $this->shared_count = 0;
        $this->referrals_count = 0;
        $this->conversions_count = 0;
                
        return $this;
    }   
}
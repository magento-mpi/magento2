<?php
/**
 * Customer dashboard block
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Block_Dashboard extends Mage_Core_Block_Template 
{
	protected $_subscription = null;
	
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('customer/dashboard.phtml');
    }
    
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
    
    public function getAccountUrl()
    {
        return Mage::getUrl('customer/account/edit', array('_secure'=>true));
    }
    
    public function getAddressesUrl()
    {
        return Mage::getUrl('customer/address/index', array('_secure'=>true));
    }
    
    public function getAddressEditUrl($address)
    {
        return Mage::getUrl('customer/address/edit', array('_secure'=>true, 'id'=>$address->getId()));
    }
    
    public function getOrdersUrl()
    {
        return Mage::getUrl('customer/order/index', array('_secure'=>true));
    }
    
    public function getReviewsUrl()
    {
        return Mage::getUrl('customer/review/index', array('_secure'=>true));
    }
    
    public function getWishlistUrl()
    {
        return Mage::getUrl('customer/wishlist/index', array('_secure'=>true));
    }
    
    public function getTagsUrl()
    {
        
    }
    
    public function getSubscriptionObject() 
    {
    	if(is_null($this->_subscription)) { 
			$this->_subscription = Mage::getModel('newsletter/subscriber')->loadByCustomer($this->getCustomer());
    	}
    	
    	return $this->_subscription;
    }
    
    public function getManageNewsletterUrl()
    {
    	return $this->getUrl('*/newsletter/manage');
    }
    
    public function getSubscriptionText() 
    {
    	if($this->getSubscriptionObject()->isSubscribed()) {
    		return __('You are currently subscribed to our newsletter');
    	}
    	
    	return _('You are currently not subscribed to our newsletter');
    }
    
    public function getPrimaryAddresses()
    {
        $addresses = $this->getCustomer()->getPrimaryAddresses();
        if (empty($addresses)) {
            return false;
        }
        return $addresses;
    }
}

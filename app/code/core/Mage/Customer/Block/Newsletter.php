<?php
/**
 * Customer front  newsletter manage block
 *
 * @package    Mage
 * @subpackage Customer
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Customer_Block_Newsletter extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('customer/form/newsletter.phtml');
	}
	
	public function getSubscriptionObject() 
    {
    	if(is_null($this->_subscription)) { 
			$this->_subscription = Mage::getModel('newsletter/subscriber')->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());
    	}
    	
    	return $this->_subscription;
    }
        
    public function getIsSubscribed()
    {
    	return $this->getSubscriptionObject()->isSubscribed();
    }
    
    public function getAction()
    {
    	return $this->getUrl('*/*/save');
    }
}// Class Mage_Customer_Block_Newsletter END
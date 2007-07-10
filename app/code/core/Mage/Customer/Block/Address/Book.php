<?php
/**
 * Customer address book block
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Block_Address_Book extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('customer/address/book.phtml');
    }
    
    public function getAddAddressUrl()
    {
        return Mage::getUrl('customer/address/new', array('_secure'=>true));
    }
    
    public function getBackUrl()
    {
        return Mage::getUrl('customer/account/index', array('_secure'=>true));
    }
    
    public function getPrimaryAddresses()
    {
        $addresses = Mage::getSingleton('customer/session')->getCustomer()->getPrimaryAddresses();
        if (empty($addresses)) {
            return false;
        }
        return 
    }
    
    public function getAdditionalAddresses()
    {
        
    }
}

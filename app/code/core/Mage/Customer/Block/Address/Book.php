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
    
    public function getDeleteUrl()
    {
        return Mage::getUrl('customer/address/delete');
    }
    
    public function getAddressEditUrl($address)
    {
        return Mage::getUrl('customer/address/edit', array('_secure'=>true, 'id'=>$address->getId()));
    }
    
    public function getPrimaryAddresses()
    {
        $addresses = Mage::getSingleton('customer/session')->getCustomer()->getPrimaryAddresses();
        return empty($addresses) ? false : $addresses;
    }
    
    public function getAdditionalAddresses()
    {
        $addresses = Mage::getSingleton('customer/session')->getCustomer()->getAdditionalAddresses();
        return empty($addresses) ? false : $addresses;
    }
    
    public function getAddressHtml($address)
    {
        return $address->toString($address->getHtmlFormat());
    }
}

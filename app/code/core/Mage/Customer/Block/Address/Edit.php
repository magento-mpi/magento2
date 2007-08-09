<?php
/**
 * Customer address edit block
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Block_Address_Edit extends Mage_Directory_Block_Data
{
    protected $_address;
    protected $_countryCollection;
    protected $_regionCollection;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('customer/address/edit.phtml');
        $this->_address = Mage::getModel('customer/address');
        
        // Init address object
        if ($id = $this->getRequest()->getParam('id')) {
            $this->_address->load($id);
            if ($this->_address->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId()) {
                $this->_address->setData(array());
            }
        }
        
        Mage::registry('action')->getLayout()->getBlock('root')->setHeaderTitle(($this->getAddress()->getId()?'Edit':'New').' Address Entry');

        if ($postedData = Mage::getSingleton('customer/session')->getAddressFormData(true)) {
            $this->_address->setData($postedData);
        }
    }
    
    public function getTitle()
    {
        return $this->getData('title');
    }
    
    public function getBackUrl()
    {
        $url = $this->getData('back_url');
        if (is_null($url)) {
            $url = Mage::getUrl('*/*/index', array('_secure'=>true));
            $this->setData('back_url', $url);
        }
        return $url;
    }
    
    public function getSaveUrl()
    {
        return Mage::getUrl('customer/address/formPost', array('_secure'=>true, 'id'=>$this->getAddress()->getId()));
    }
    
    public function getAddress()
    {
        return $this->_address;
    }
    
    public function getCountryId()
    {
        return $this->getAddress()->getCountryId();
    }
    
    public function getRegionId()
    {
        return $this->getAddress()->getRegionId();
    }
    
    public function getCustomerAddressCount()
    {
        return Mage::getSingleton('customer/session')->getCustomer()
            ->getLoadedAddressCollection()
            ->getSize();
    }
    
    public function canSetAsDefaultBilling()
    {
        if (!$this->getAddress()->getId()) {
            return $this->getCustomerAddressCount();
        }
        return !$this->isDefaultBilling();
    }
    
    public function canSetAsDefaultShipping()
    {
        if (!$this->getAddress()->getId()) {
            return $this->getCustomerAddressCount();
        }
        return !$this->isDefaultShipping();;
    }
    
    public function isDefaultBilling()
    {
        return $this->getAddress()->getId()==Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
    }
    
    public function isDefaultShipping()
    {
        return $this->getAddress()->getId()==Mage::getSingleton('customer/session')->getCustomer()->getDefaultShipping();
    }
}

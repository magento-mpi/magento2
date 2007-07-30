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
class Mage_Customer_Block_Address_Edit extends Mage_Core_Block_Template 
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
    
    public function getBackUrl()
    {
        return Mage::getUrl('*/*/index', array('_secure'=>true));
    }
    
    public function getSaveUrl()
    {
        return Mage::getUrl('*/*/formPost', array('_secure'=>true, 'id'=>$this->getAddress()->getId()));
    }
    
    public function getAddress()
    {
        return $this->_address;
    }
    
    public function getCountryCollection()
    {
        if (!$this->_countryCollection) {
            $this->_countryCollection = Mage::getModel('directory/country')->getResourceCollection()
                ->load();
        }
        return $this->_countryCollection;
    }
    
    public function getCountryHtmlSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('country_id')
            ->setId('country')
            ->setTitle('Country')
            ->setClass('validate-select')
            ->setValue($this->getAddress()->getCountryId())
            ->setOptions($this->getCountryCollection()->toOptionArray())
            ->getHtml();
    }
    
    public function getRegionCollection()
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($this->getAddress()->getCountryId())
                ->load();
        }
        return $this->_regionCollection;
    }
    
    public function getRegionHtmlSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('region')
            ->setTitle('State/Province')
            ->setId('state')
            ->setClass('required-entry validate-state input-text')
            ->setValue($this->getAddress()->getRegionId())
            ->setOptions($this->getREgionCollection()->toOptionArray())
            ->getHtml();
    }
    
    public function canSetAsDefaultBilling()
    {
        if (!$this->getAddress()->getId()) {
            return true;
        }
        return !($this->getAddress()->getId()==Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling());
    }
    
    public function canSetAsDefaultShipping()
    {
        if (!$this->getAddress()->getId()) {
            return true;
        }
        return !($this->getAddress()->getId()==Mage::getSingleton('customer/session')->getCustomer()->getDefaultShipping());
    }
}

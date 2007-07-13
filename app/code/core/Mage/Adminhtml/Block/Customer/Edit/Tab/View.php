<?php
/**
 * Customer account form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View extends Mage_Core_Block_Template
{
    protected $_customer;
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/customer/tab/view.phtml');
    }
    
    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::registry('customer');
        }
        return $this->_customer;
    }
    
    public function getCreateDate()
    {
        return $this->getCustomer()->getCreatedAt();
    }
    
    public function getLastLoginDate()
    {
        
    }
    
    public function getCurrentStatus()
    {
        
    }
    
    public function getBillingAddressHtml()
    {
        $html = '';
        if ($address = $this->getCustomer()->getPrimaryBillingAddress()) {
            $html = $address->toString($address->getHtmlFormat());
        }
        else {
            $html = __('Customer do not have primary billing address');
        }
        return $html;
    }
}

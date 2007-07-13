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
    
    protected function _initChildren()
    {
        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
            ->setId('customerViewAcc');
        
        /* @var $accordion Mage_Adminhtml_Block_Widget_Accordion */
        $accordion->addItem('lastOrders', array(
            'title'     => __('Last %s orders', 5),
            //'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_orders')->setId('last5orders'),
            'content'   => 'Last orders',
            'open'      => true
        ));
        
        $accordion->addItem('shopingCart', array(
            'title'     => __('Shopping Cart'),
            'content'   => 'cart'
        ));
        
        $accordion->addItem('wishlist', array(
            'title'     => __('Wishlist'),
            'content'   => 'Wishlist'
        ));
        $this->setChild('accordion', $accordion);
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
    
    public function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }
}

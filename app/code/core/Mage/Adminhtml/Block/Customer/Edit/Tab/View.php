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
    const ONLINE_INTERVAL = 900; // 15 min
    protected $_customer;
    protected $_customerLog;
    
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
            'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_view_orders')->setId('last5orders'),
            'open'      => true
        ));

        $accordion->addItem('shopingCart', array(
            'title'         => __('Shopping Cart'),
            //'content_url'   => Mage::getBaseUrl(),
            //'ajax'          => true,
            'content'       => 'cart'
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
    
    public function getCustomerLog()
    {
        if (!$this->_customerLog) {
            $this->_customerLog = Mage::getModel('log/customer')
                ->load($this->getCustomer()->getId());
        }
        return $this->_customerLog;
    }

    public function getCreateDate()
    {
        return $this->getCustomer()->getCreatedAt();
    }

    public function getLastLoginDate()
    {
        return $this->getCustomerLog()->getLoginAt();
    }

    public function getCurrentStatus()
    {
        $log = $this->getCustomerLog();
        if ($log->getLogoutAt() || strtotime(now())-strtotime($log->getLastVisitAt())>self::ONLINE_INTERVAL) {
            return __('Offline');
        }
        return __('Online');
    }
    
    public function getCreatedInStore()
    {
        return Mage::getModel('core/store')->load($this->getCustomer()->getStoreId())->getName();
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

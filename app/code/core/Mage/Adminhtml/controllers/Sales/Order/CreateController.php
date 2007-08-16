<?php
/**
 * Adminhtml sales orders creation process controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Sales_Order_CreateController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Enter description here...
     *
     * @var Mage_Adminhtml_Model_Quote
     */
    protected $_session = null;

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Model_Quote
     */
    public function getSession()
    {
        if (is_null($this->_session)) {
             $this->_session = Mage::getSingleton('adminhtml/quote');
        }
        return $this->_session;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getSession()->getQuote();
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Sales_Order_CreateController
     */
    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Orders'), __('Orders'))
            ->_addBreadcrumb(__('Create Order'), __('Create Order'))
        ;
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->getSession()->setCustomerId($customerId);
        }

        $this->getLayout()->getBlock('left')->setIsCollapsed(true);

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('core/template')->setTemplate('sales/order/create/jsbefore.phtml'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_order_create'))
            ->_addContent($this->getLayout()->createBlock('core/template')->setTemplate('sales/order/create/jsafter.phtml'))
            ->_addLeft($this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar'))
            ->renderLayout();
    }

    public function startAction()
    {
        $this->getSession()->reset();
        $this->_redirect('*/*');
    }

    public function cancelAction()
    {
        $this->getSession()->reset();
        $this->_redirect('*/*');
    }

    public function customerAction()
    {
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->getSession()->setCustomerId($customerId);
            $this->getResponse()->setBody('<script type="text/javascript">$("sc_customer_name").innerHTML="' . __('for') . ' ' . $this->getSession()->getCustomerName() . '"; $("sc_customer_name").show();</script>');
        } else {
            $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_customer')->toHtml());
        }
    }

    public function customerGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_customer_grid')->toHtml());
    }

    public function storeAction()
    {
        if ($storeId = $this->getRequest()->getParam('store_id')) {
//            if ($this->getSession()->getIsOldCustomer() && (! in_array($storeId, $this->getSession()->getCustomer()->getSharedStoreIds())) {
            $this->getSession()->setStoreId($storeId);
            $this->getResponse()->setBody('<script type="text/javascript">$("sc_store_name").innerHTML="' . __('in') . ' ' . $this->getSession()->getQuote()->getStore()->getName() . '"; $("sc_store_name").show();</script>');
        } else {
            $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_store')->toHtml());
        }
    }

    public function sidebarAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar')->toHtml());
    }

    public function cartAction()
    {
        $intFilter = new Zend_Filter_Int();

        // customer's front-end quote
    	$customerQuote = $this->getSession()->getCustomerQuote();


        //remove items from admin quote
        $ids = Zend_Json::decode($this->getRequest()->getParam('move'));
        if (is_array($ids)) {
            foreach ($ids as $id) {
                if ($itemId = $intFilter->filter($id)) {
                    // add items to frontend quote
                    // TODO - if customer is not created yet
                    if ($customerQuote && ($item = $this->getQuote()->getItemById($itemId))) {
                        $newItem = clone $item;
                        $customerQuote->addItem($item);
                    }
                    $this->getQuote()->removeItem($itemId);
                }
            }
        }

        // update frontend quote
        if ($customerQuote) {
            $customerQuote->getShippingAddress()->collectTotals(); // ?
            $customerQuote->save();
        }

        // update admin quote
    	$this->getQuote()->getShippingAddress()->collectTotals();
    	$this->getQuote()->save();

        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_cart')->toHtml());
    }

    public function wishlistAction()
    {

        $wishlist = null;

        try {
            $wishlist = Mage::getModel('wishlist/wishlist');
            /* @var $wishlist Mage_Wishlist_Model_Wishlist */
            $wishlist->loadByCustomer($this->getSession()->getCustomer(), true);
    		$wishlist->addNewItem($this->getRequest()->getParam('product'));
        } catch (Exception $e) {
            // TODO - if customer is not created yet
        }

        //remove items from admin quote
        $ids = Zend_Json::decode($this->getRequest()->getParam('move'));
        if (is_array($ids)) {
            foreach ($ids as $id) {
                if ($itemId = $intFilter->filter($id)) {
                    // add items to customer's wishlist
                    // TODO - if customer is not created yet
                    if ($wishlist && ($item = $this->getQuote()->getItemById($itemId))) {
                        $wishlist->addNewItem($item->getProductId());
                    }


                    if ($customerQuote && ($item = $this->getQuote()->getItemById($itemId))) {
                        $newItem = clone $item;
                        $customerQuote->addItem($item);
                    }
                    $this->getQuote()->removeItem($itemId);
                }
            }
        }



        $intFilter = new Zend_Filter_Int();

        //remove items
        $ids = $intFilter->filter($this->getRequest()->getParam('move'));
        if (!empty($ids)) {
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    $this->getQuote()->removeItem($id);
                }
            } else {
                $this->getQuote()->removeItem($ids);
            }
        }

    	$this->getQuote()->getShippingAddress()->collectTotals();
    	$this->getQuote()->save();

        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_wishlist')->toHtml());
    }

    public function viewedAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_viewed')->toHtml());
    }

    public function comparedAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_compared')->toHtml());
    }

}

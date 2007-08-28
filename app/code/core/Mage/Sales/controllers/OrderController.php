<?php
/**
 * Sales orders controller
 *
 * @package    Mage
 * @subpackage Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Sales_OrderController extends Mage_Core_Controller_Front_Action
{

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::getUrl('*/*/login', array('_secure'=>true, '_current'=>true));
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    public function historyAction()
    {
        $this->loadLayout(array('default', 'customer_account'), 'customer_account');

        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('sales/order_history', 'sales.order.history'));

        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout(array('default', 'customer_account'), 'customer_account');

        Mage::register('order_id', $this->getRequest()->getParam('order_id', false));

        $block = $this->getLayout()->createBlock('sales/order_view', 'sales.order.view');
        $this->getLayout()->getBlock('content')->append($block);

        $this->renderLayout();
    }

    public function detailsAction()
    {
        $this->loadLayout(array('default', 'customer_account'), 'customer_account');

        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('sales/order_details', 'sales.order.details'));

        $this->renderLayout();
    }

    public function printAction()
    {
        $this->loadLayout('print');

        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('sales/order_details', 'sales.order.details'));

        $this->renderLayout();
    }

}

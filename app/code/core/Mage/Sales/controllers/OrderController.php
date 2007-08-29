<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales orders controller
 *
 * @category   Mage
 * @package    Mage_Sales
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

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales transactions controller
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Sales_TransactionsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize payment transaction model
     *
     * @return Mage_Sales_Model_Order_Payment_Transaction | bool
     */
    protected function _initTransaction()
    {
        $txn = Mage::getModel('Mage_Sales_Model_Order_Payment_Transaction')->load(
            $this->getRequest()->getParam('txn_id')
        );

        if (!$txn->getId()) {
            $this->_getSession()->addError($this->__('Wrong transaction ID specified.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $txn->setOrderUrl(
                $this->getUrl('*/sales_order/view', array('order_id' => $orderId))
            );
        }

        Mage::register('current_transaction', $txn);
        return $txn;
    }

    public function indexAction()
    {
        $this->_title($this->__('Sales'))
            ->_title($this->__('Transactions'));

        $this->loadLayout()
            ->_setActiveMenu('sales/transactions')
            ->renderLayout();
    }

    /**
     * Ajax grid action
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * View Transaction Details action
     */
    public function viewAction()
    {
        $txn = $this->_initTransaction();
        if (!$txn) {
            return;
        }
        $this->_title($this->__('Sales'))
            ->_title($this->__('Transactions'))
            ->_title(sprintf("#%s", $txn->getTxnId()));

        $this->loadLayout()
            ->_setActiveMenu('sales/transactions')
            ->renderLayout();
    }

    /**
     * Fetch transaction details action
     */
    public function fetchAction()
    {
        $txn = $this->_initTransaction();
        if (!$txn) {
            return;
        }
        try {
            $txn->getOrderPaymentObject()
                ->setOrder($txn->getOrder())
                ->importTransactionInfo($txn);
            $txn->save();
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(
                Mage::helper('Mage_Adminhtml_Helper_Data')->__('The transaction details have been updated.')
            );
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(
                Mage::helper('Mage_Adminhtml_Helper_Data')->__('Unable to update transaction details.')
            );
            Mage::logException($e);
        }
        $this->_redirect('*/sales_transactions/view', array('_current' => true));
    }

    /**
     * Check currently called action by permissions for current user
     *
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'fetch':
                return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/transactions/fetch');
                break;
            default:
                return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/transactions');
                break;
        }
    }
}

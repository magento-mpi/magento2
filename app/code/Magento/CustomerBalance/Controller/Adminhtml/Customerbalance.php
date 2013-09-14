<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Controller for Customer account -> Store Credit ajax tab and all its contents
 *
 */
namespace Magento\CustomerBalance\Controller\Adminhtml;

class Customerbalance extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Check is enabled module in config
     *
     * @return \Magento\CustomerBalance\Controller\Adminhtml\Customerbalance
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!\Mage::helper('Magento\CustomerBalance\Helper\Data')->isEnabled()) {
            if ($this->getRequest()->getActionName() != 'noroute') {
                $this->_forward('noroute');
            }
        }
        return $this;
    }

    /**
     * Customer balance form
     *
     */
    public function formAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer balance grid
     *
     */
    public function gridHistoryAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance\Balance\History\Grid'
            )->toHtml()
        );
    }

    /**
     * Delete orphan balances
     *
     */
    public function deleteOrphanBalancesAction()
    {
        $balance = \Mage::getSingleton('Magento\CustomerBalance\Model\Balance')->deleteBalancesByCustomerId(
            (int)$this->getRequest()->getParam('id')
        );
        $this->_redirect('*/customer/edit/', array('_current'=>true));
    }

    /**
     * Instantiate customer model
     *
     * @param string $idFieldName
     */
    protected function _initCustomer($idFieldName = 'id')
    {
        $customer = \Mage::getModel('Magento\Customer\Model\Customer')->load((int)$this->getRequest()->getParam($idFieldName));
        if (!$customer->getId()) {
            \Mage::throwException(__('Failed to initialize customer'));
        }
        \Mage::register('current_customer', $customer);
    }

    /**
     * Check is allowed customer management
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Customer::manage');
    }
}

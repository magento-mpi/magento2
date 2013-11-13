<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml billing agreement controller
 */
namespace Magento\Sales\Controller\Adminhtml\Billing;

class Agreement extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\App\Action\Title
     */
    protected $_title;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\App\Action\Title $title
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\App\Action\Title $title
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->_title = $title;
    }

    /**
     * Billing agreements
     *
     */
    public function indexAction()
    {
        $this->_title->add(__('Billing Agreements'));

        $this->_layoutServices->loadLayout();
        $this->_setActiveMenu('Magento_Sales::sales_billing_agreement');
        $this->_layoutServices->renderLayout();
    }

    /**
     * Ajax action for billing agreements
     *
     */
    public function gridAction()
    {
        $this->_layoutServices->loadLayout(false);
        $this->_layoutServices->renderLayout();
    }

    /**
     * View billing agreement action
     *
     */
    public function viewAction()
    {
        $agreementModel = $this->_initBillingAgreement();

        if ($agreementModel) {
            $this->_title->add(__('Billing Agreements'))
                ->_title->add(sprintf("#%s", $agreementModel->getReferenceId()));

            $this->_layoutServices->loadLayout();
            $this->_setActiveMenu('Magento_Sales::sales_billing_agreement');
            $this->_layoutServices->renderLayout();
            return;
        }

        $this->_redirect('sales/*/');
        return;
    }

    /**
     * Related orders ajax action
     *
     */
    public function ordersGridAction()
    {
        $this->_initBillingAgreement();
        $this->_layoutServices->loadLayout(false);
        $this->_layoutServices->renderLayout();
    }

    /**
     * Customer billing agreements ajax action
     *
     */
    public function customerGridAction()
    {
        $this->_initCustomer();
        $this->_layoutServices->loadLayout(false);
        $this->_layoutServices->renderLayout();
    }

    /**
     * Cancel billing agreement action
     *
     */
    public function cancelAction()
    {
        $agreementModel = $this->_initBillingAgreement();

        if ($agreementModel && $agreementModel->canCancel()) {
            try {
                $agreementModel->cancel();
                $this->_getSession()->addSuccess(__('You canceled the billing agreement.'));
                $this->_redirect('sales/*/view', array('_current' => true));
                return;
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addError(__('We could not cancel the billing agreement.'));
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
            $this->_redirect('sales/*/view', array('_current' => true));
        }
        return $this->_redirect('sales/*/');
    }

    /**
     * Delete billing agreement action
     */
    public function deleteAction()
    {
        $agreementModel = $this->_initBillingAgreement();

        if ($agreementModel) {
            try {
                $agreementModel->delete();
                $this->_getSession()->addSuccess(__('You deleted the billing agreement.'));
                $this->_redirect('sales/*/');
                return;
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addError(__('We could not delete the billing agreement.'));
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
            $this->_redirect('sales/*/view', array('_current' => true));
        }
        $this->_redirect('sales/*/');
    }

    /**
     * Initialize billing agreement by ID specified in request
     *
     * @return \Magento\Sales\Model\Billing\Agreement | false
     */
    protected function _initBillingAgreement()
    {
        $agreementId = $this->getRequest()->getParam('agreement');
        $agreementModel = $this->_objectManager->create('Magento\Sales\Model\Billing\Agreement')->load($agreementId);

        if (!$agreementModel->getId()) {
            $this->_getSession()->addError(__('Please specify the correct billing agreement ID and try again.'));
            return false;
        }

        $this->_coreRegistry->register('current_billing_agreement', $agreementModel);
        return $agreementModel;
    }

    /**
     * Initialize customer by ID specified in request
     *
     * @return \Magento\Sales\Controller\Adminhtml\Billing\Agreement
     */
    protected function _initCustomer()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        $this->_coreRegistry->register('current_customer', $customer);
        return $this;
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'index':
            case 'grid' :
            case 'view' :
                return $this->_authorization->isAllowed('Magento_Sales::billing_agreement_actions_view');
            case 'cancel':
            case 'delete':
                return $this->_authorization->isAllowed('Magento_Sales::actions_manage');
            default:
                return $this->_authorization->isAllowed('Magento_Sales::billing_agreement');
        }
    }
}

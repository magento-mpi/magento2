<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring payments view/management controller
 */
namespace Magento\RecurringPayment\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Customer\Controller\RegistryConstants;

class RecurringPayment extends \Magento\Framework\App\Action\Action
{
    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->_customerSession = $customerSession;
    }

    /**
     * Make sure customer is logged in and put it into registry
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$request->isDispatched()) {
            return parent::dispatch($request);
        }
        if (!$this->_customerSession->authenticate($this)) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        $customer = $this->_customerSession->getCustomer();
        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER, $customer);
        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customer->getId());
        return parent::dispatch($request);
    }

    /**
     * Generic payment view action
     *
     * @return void
     */
    protected function _viewAction()
    {
        try {
            $payment = $this->_initPayment();
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();

            $title = __('Recurring Payment #%1', $payment->getReferenceId());
            $this->_view->getPage()->getConfig()->getTitle()->set($title);

            $this->_view->renderLayout();
            return;
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Instantiate current payment and put it into registry
     *
     * @return \Magento\RecurringPayment\Model\Payment
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _initPayment()
    {
        $payment = $this->_objectManager->create(
            'Magento\RecurringPayment\Model\Payment'
        )->load(
            $this->getRequest()->getParam('payment')
        );
        if (!$payment->getId() || $payment->getCustomerId() != $this->_customerSession->getId()) {
            throw new \Magento\Framework\Model\Exception(__('We can\'t find the payment you specified.'));
        }
        $this->_coreRegistry->register('current_recurring_payment', $payment);
        return $payment;
    }
}

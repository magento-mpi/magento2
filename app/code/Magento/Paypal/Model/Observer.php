<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal module observer
 */
namespace Magento\Paypal\Model;

class Observer
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * Paypal hss
     *
     * @var \Magento\Paypal\Helper\Hss
     */
    protected $_paypalHss;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Paypal\Model\Report\SettlementFactory
     */
    protected $_settlementFactory;

    /**
     * @var \Magento\App\ViewInterface
     */
    protected $_view;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Paypal\Model\Billing\AgreementFactory
     */
    protected $_agreementFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Paypal\Helper\Hss $paypalHss
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Logger $logger
     * @param Report\SettlementFactory $settlementFactory
     * @param \Magento\App\ViewInterface $view
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\Paypal\Model\Billing\AgreementFactory $agreementFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Paypal\Helper\Hss $paypalHss,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Logger $logger,
        \Magento\Paypal\Model\Report\SettlementFactory $settlementFactory,
        \Magento\App\ViewInterface $view,
        \Magento\AuthorizationInterface $authorization,
        \Magento\Paypal\Model\Billing\AgreementFactory $agreementFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_coreData = $coreData;
        $this->_paypalHss = $paypalHss;
        $this->_coreRegistry = $coreRegistry;
        $this->_logger = $logger;
        $this->_settlementFactory = $settlementFactory;
        $this->_view = $view;
        $this->_authorization = $authorization;
        $this->_agreementFactory = $agreementFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
    }

    /**
     * Goes to reports.paypal.com and fetches Settlement reports.
     * @return \Magento\Paypal\Model\Observer
     */
    public function fetchReports()
    {
        try {
            /** @var \Magento\Paypal\Model\Report\Settlement $reports */
            $reports = $this->_settlementFactory->create();
            /* @var $reports \Magento\Paypal\Model\Report\Settlement */
            $credentials = $reports->getSftpCredentials(true);
            foreach ($credentials as $config) {
                try {
                    $reports->fetchAndSave(\Magento\Paypal\Model\Report\Settlement::createConnection($config));
                } catch (\Exception $e) {
                    $this->_logger->logException($e);
                }
            }
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }
    }

    /**
     * Clean unfinished transaction
     *
     * @deprecated since 1.6.2.0
     * @return \Magento\Paypal\Model\Observer
     */
    public function cleanTransactions()
    {
        return $this;
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Paypal\Model\Observer
     */
    public function saveOrderAfterSubmit(\Magento\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getData('order');
        $this->_coreRegistry->register('hss_order', $order, true);

        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Paypal\Model\Observer
     */
    public function setResponseAfterSaveOrder(\Magento\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $this->_coreRegistry->registry('hss_order');

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && in_array($payment->getMethod(), $this->_paypalHss->getHssMethods())) {
                /* @var $controller \Magento\App\Action\Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = $this->_coreData->jsonDecode($controller->getResponse()->getBody('default'));

                if (empty($result['error'])) {
                    $this->_view->loadLayout('checkout_onepage_review');
                    $html = $this->_view->getLayout()->getBlock('paypal.iframe')->toHtml();
                    $result['update_section'] = array(
                        'name' => 'paypaliframe',
                        'html' => $html
                    );
                    $result['redirect'] = false;
                    $result['success'] = false;
                    $controller->getResponse()->clearHeader('Location');
                    $controller->getResponse()->setBody($this->_coreData->jsonEncode($result));
                }
            }
        }

        return $this;
    }

    /**
     * Block admin ability to use customer billing agreements
     *
     * @param \Magento\Event\Observer $observer
     */
    public function restrictAdminBillingAgreementUsage($observer)
    {
        $event = $observer->getEvent();
        $methodInstance = $event->getMethodInstance();
        if ($methodInstance instanceof \Magento\Paypal\Model\Payment\Method\Billing\AbstractAgreement
            && false == $this->_authorization->isAllowed('Magento_Paypal::use')
        ) {
            $event->getResult()->isAvailable = false;
        }
    }

    /**
     * @param \Magento\Event\Observer $observer
     */
    public function addBillingAgreementToSession(\Magento\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Payment $orderPayment */
        $orderPayment = $observer->getEvent()->getPayment();
        if ($orderPayment->getBillingAgreementData()) {
            $order = $orderPayment->getOrder();
            $agreement = $this->_agreementFactory->create()->importOrderPayment($orderPayment);
            if ($agreement->isValid()) {
                $message = __('Created billing agreement #%1.', $agreement->getReferenceId());
                $order->addRelatedObject($agreement);
                $this->_checkoutSession->setBillingAgreement($agreement);
                $this->_checkoutSession->setLastBillingAgreementId($agreement->getId());
            } else {
                $message = __('We couldn\'t create a billing agreement for this order.');
            }
            $comment = $order->addStatusHistoryComment($message);
            $order->addRelatedObject($comment);
        }
    }

    /**
     * Prepare last billing agreement and add its related data to block
     *
     * @param \Magento\Event\Observer $observer
     */
    public function prepareLastBillingAgreementOnOrderSuccess(\Magento\Event\Observer $observer)
    {
        /** @var \Magento\Checkout\Block\Onepage\Success $block */
        $block = $observer->getEvent()->getData('block');
        $agreementId = $this->_checkoutSession->getLastBillingAgreementId();
        $customerId = $this->_customerSession->getCustomerId();
        if (!$agreementId || !$customerId) {
            return;
        }
        $agreement = $this->_agreementFactory->create()->load($agreementId);
        if ($agreement->getId() && $customerId == $agreement->getCustomerId()) {
            $block->addData(array(
                'agreement_ref_id' => $agreement->getReferenceId(),
                'agreement_url'    => $block->getUrl('sales/billing_agreement/view', array('agreement' => $agreementId))
            ));
        }
    }
}

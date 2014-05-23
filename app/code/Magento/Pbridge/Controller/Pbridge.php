<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Index controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Controller;

class Pbridge extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Logger $logger
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Load only action layout handles
     *
     * @return $this
     */
    protected function _initActionLayout()
    {
        $this->_view->addActionLayoutHandles();
        $this->_view->loadLayoutUpdates();
        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();
        $this->_view->setIsLayoutLoaded(true);
        return $this;
    }

    /**
     * Index Action.
     * Forward to result action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('result');
    }

    /**
     * Iframe Ajax Action
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function iframeAction()
    {
        $methodCode = $this->getRequest()->getParam('method_code', null);
        if ($methodCode) {
            $methodInstance = $this->_objectManager->get(
                'Magento\Payment\Helper\Data'
            )->getMethodInstance(
                $methodCode
            );
            if ($methodInstance) {
                $block = $this->_view->getLayout()->createBlock($methodInstance->getFormBlockType());
                $block->setMethod($methodInstance);
                if ($this->getRequest()->getParam('data')) {
                    $block->setFormParams($this->getRequest()->getParam('data', null));
                }
                if ($block) {
                    $this->getResponse()->setBody($block->getIframeBlock()->toHtml());
                }
            }
        } else {
            throw new \Magento\Framework\Model\Exception(__('Payment Method Code is not passed.'));
        }
    }

    /**
     * Iframe Ajax Action for review page
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function reviewAction()
    {
        $methodCode = $this->getRequest()->getParam('method_code', null);
        if ($methodCode) {
            $methodInstance = $this->_objectManager->get(
                'Magento\Payment\Helper\Data'
            )->getMethodInstance(
                $methodCode
            );
            if ($methodInstance) {
                $block = $this->_view->getLayout()->createBlock(
                    'Magento\Pbridge\Block\Checkout\Payment\Review\Iframe'
                );
                $block->setMethod($methodInstance);
                if ($block) {
                    $this->getResponse()->setBody($block->getIframeBlock()->toHtml());
                }
            }
        } else {
            throw new \Magento\Framework\Model\Exception(__('Payment Method Code is not passed.'));
        }
    }

    /**
     * Review success action
     *
     * @return void
     */
    public function successAction()
    {
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }

    /**
     * Redirect to Onepage checkout success page
     */
    public function onepagesuccessAction()
    {
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }

    /**
     * Review success action
     */
    public function cancelAction()
    {
        try {
            // if there is an order - cancel it
            $orderId = $this->_checkoutSession->getLastOrderId();
            /** @var \Magento\Sales\Model\Order $order */
            $order = $orderId ? $this->_orderFactory->create()->load($orderId) : false;
            if ($order && $order->getId() && $order->getQuoteId() == $this->_checkoutSession->getQuoteId()) {
                $order->cancel()->save();
                $this->_checkoutSession
                    ->unsLastQuoteId()
                    ->unsLastSuccessQuoteId()
                    ->unsLastOrderId()
                    ->unsLastRealOrderId()
                    ->addSuccess(__('Order has been canceled.'))
                ;
            } else {
                $this->_checkoutSession->addSuccess(__('Order has been canceled.'));
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->_checkoutSession->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_checkoutSession->addError(__('Unable to cancel order.'));
            $this->_logger->logException($e);
        }

        $this->_initActionLayout();
        $this->_view->renderLayout();
    }

    /**
     * Review error action
     *
     * @return void
     */
    public function errorAction()
    {
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }

    /**
     * Result Action
     *
     * @return void
     */
    public function resultAction()
    {
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }

    /**
     * Validate all agreements
     * (terms and conditions are agreed)
     *
     * @return void
     */
    public function validateAgreementAction()
    {
        $result = array();
        $result['success'] = true;
        $agreementsValidator = $this->_objectManager->get('Magento\Checkout\Model\Agreements\AgreementsValidator');
        if (!$agreementsValidator->isValid(array_keys($this->getRequest()->getPost('agreement', [])))) {
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = __(
                'Please agree to all the terms and conditions before placing the order.'
            );
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
    }
}

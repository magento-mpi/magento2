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
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Paypal\Helper\Hss $paypalHss
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Logger $logger
     * @param Report\SettlementFactory $settlementFactory
     * @param \Magento\App\ViewInterface $view
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Paypal\Helper\Hss $paypalHss,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Logger $logger,
        \Magento\Paypal\Model\Report\SettlementFactory $settlementFactory,
        \Magento\App\ViewInterface $view
    ) {
        $this->_coreData = $coreData;
        $this->_paypalHss = $paypalHss;
        $this->_coreRegistry = $coreRegistry;
        $this->_logger = $logger;
        $this->_settlementFactory = $settlementFactory;
        $this->_view = $view;
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
     * Add PayPal shortcut buttons
     *
     * @param \Magento\Event\Observer $observer
     */
    public function addPaypalShortcuts(\Magento\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Block\ShortcutButtons $shortcutButtons */
        $shortcutButtons = $observer->getEvent()->getContainer();
        // PayPal Express Checkout
        $shortcut = $shortcutButtons->getLayout()->createBlock(
            'Magento\Paypal\Block\Express\Shortcut',
            '',
            array('checkoutSession' => $observer->getEvent()->getCheckoutSession())
        );
        $shortcut->setIsInCatalogProduct($observer->getEvent()->getIsCatalogProduct())
            ->setShowOrPosition($observer->getEvent()->getOrPosition())
            ->setTemplate('express/shortcut.phtml');
        $shortcutButtons->addShortcut($shortcut);
        // PayPal Express Checkout Payflow Edition
        $shortcut = $shortcutButtons->getLayout()->createBlock(
            'Magento\Paypal\Block\PayflowExpress\Shortcut',
            '',
            array('checkoutSession' => $observer->getEvent()->getCheckoutSession())
        );
        $shortcut->setIsInCatalogProduct($observer->getEvent()->getIsCatalogProduct())
            ->setShowOrPosition($observer->getEvent()->getOrPosition())
            ->setTemplate('express/shortcut.phtml');
        $shortcutButtons->addShortcut($shortcut);
    }
}

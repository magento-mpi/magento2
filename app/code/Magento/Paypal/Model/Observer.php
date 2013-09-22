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
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Paypal\Model;

class Observer
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Paypal hss
     *
     * @var \Magento\Paypal\Helper\Hss
     */
    protected $_paypalHss = null;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Paypal\Helper\Hss $paypalHss
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param Magento_Core_Model_Logger $logger
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Paypal_Helper_Hss $paypalHss,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Logger $logger
    ) {
        $this->_coreData = $coreData;
        $this->_paypalHss = $paypalHss;
        $this->_coreRegistry = $coreRegistry;
        $this->_logger = $logger;
    }

    /**
     * Goes to reports.paypal.com and fetches Settlement reports.
     * @return \Magento\Paypal\Model\Observer
     */
    public function fetchReports()
    {
        try {
            $reports = \Mage::getModel('Magento\Paypal\Model\Report\Settlement');
            /* @var $reports \Magento\Paypal\Model\Report\Settlement */
            $credentials = $reports->getSftpCredentials(true);
            foreach ($credentials as $config) {
                try {
                    $reports->fetchAndSave(Magento_Paypal_Model_Report_Settlement::createConnection($config));
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
                /* @var $controller \Magento\Core\Controller\Varien\Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = $this->_coreData->jsonDecode(
                    $controller->getResponse()->getBody('default'),
                    \Zend_Json::TYPE_ARRAY
                );

                if (empty($result['error'])) {
                    $controller->loadLayout('checkout_onepage_review');
                    $html = $controller->getLayout()->getBlock('paypal.iframe')->toHtml();
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
}

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

class Magento_Paypal_Model_Observer
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Paypal hss
     *
     * @var Magento_Paypal_Helper_Hss
     */
    protected $_paypalHss = null;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Paypal_Helper_Hss $paypalHss
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Paypal_Helper_Hss $paypalHss,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreData = $coreData;
        $this->_paypalHss = $paypalHss;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Goes to reports.paypal.com and fetches Settlement reports.
     * @return Magento_Paypal_Model_Observer
     */
    public function fetchReports()
    {
        try {
            $reports = Mage::getModel('Magento_Paypal_Model_Report_Settlement');
            /* @var $reports Magento_Paypal_Model_Report_Settlement */
            $credentials = $reports->getSftpCredentials(true);
            foreach ($credentials as $config) {
                try {
                    $reports->fetchAndSave(Magento_Paypal_Model_Report_Settlement::createConnection($config));
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Clean unfinished transaction
     *
     * @deprecated since 1.6.2.0
     * @return Magento_Paypal_Model_Observer
     */
    public function cleanTransactions()
    {
        return $this;
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Paypal_Model_Observer
     */
    public function saveOrderAfterSubmit(Magento_Event_Observer $observer)
    {
        /* @var $order Magento_Sales_Model_Order */
        $order = $observer->getEvent()->getData('order');
        $this->_coreRegistry->register('hss_order', $order, true);

        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Paypal_Model_Observer
     */
    public function setResponseAfterSaveOrder(Magento_Event_Observer $observer)
    {
        /* @var $order Magento_Sales_Model_Order */
        $order = $this->_coreRegistry->registry('hss_order');

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && in_array($payment->getMethod(), $this->_paypalHss->getHssMethods())) {
                /* @var $controller Magento_Core_Controller_Varien_Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = $this->_coreData->jsonDecode(
                    $controller->getResponse()->getBody('default'),
                    Zend_Json::TYPE_ARRAY
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

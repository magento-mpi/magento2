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
                    $reports->fetchAndSave(\Magento\Paypal\Model\Report\Settlement::createConnection($config));
                } catch (\Exception $e) {
                    \Mage::logException($e);
                }
            }
        } catch (\Exception $e) {
            \Mage::logException($e);
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
        \Mage::register('hss_order', $order, true);

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
        $order = \Mage::registry('hss_order');

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && in_array($payment->getMethod(), \Mage::helper('Magento\Paypal\Helper\Hss')->getHssMethods())) {
                /* @var $controller \Magento\Core\Controller\Varien\Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = \Mage::helper('Magento\Core\Helper\Data')->jsonDecode(
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
                    $controller->getResponse()->setBody(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result));
                }
            }
        }

        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * 3D Secure Validation Model
 *
 * @category   Magento
 * @package    Magento_Centinel
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Centinel_Model_Observer extends \Magento\Object
{
    /**
     * Set cmpi data to payment
     *
     * @param \Magento\Object $observer
     * @return Magento_Centinel_Model_Observer
     */
    public function salesEventConvertQuoteToOrder($observer)
    {
        $payment = $observer->getEvent()->getQuote()->getPayment();

        if ($payment->getMethodInstance()->getIsCentinelValidationEnabled()) {
            $to = array($payment, 'setAdditionalInformation');
            $payment->getMethodInstance()->getCentinelValidator()->exportCmpiData($to);
        }
        return $this;
    }

    /**
     * Add cmpi data to info block
     *
     * @param \Magento\Object $observer
     * @return Magento_Centinel_Model_Observer
     */
    public function paymentInfoBlockPrepareSpecificInformation($observer)
    {
        if ($observer->getEvent()->getBlock()->getIsSecureMode()) {
            return;
        }

        $payment = $observer->getEvent()->getPayment();
        $transport = $observer->getEvent()->getTransport();
        $helper = Mage::helper('Magento_Centinel_Helper_Data');

        $info = array(
            Magento_Centinel_Model_Service::CMPI_PARES,
            Magento_Centinel_Model_Service::CMPI_ENROLLED,
            Magento_Centinel_Model_Service::CMPI_ECI,
            Magento_Centinel_Model_Service::CMPI_CAVV,
            Magento_Centinel_Model_Service::CMPI_XID
        );
        foreach ($info as $key) {
            if ($value = $payment->getAdditionalInformation($key)) {
                $transport->setData($helper->getCmpiLabel($key), $helper->getCmpiValue($key, $value));
            }
        }
        return $this;
    }

    /**
     * Add centinel logo block into payment form
     *
     * @param \Magento\Object $observer
     * @return Magento_Centinel_Model_Observer
     */
    public function paymentFormBlockToHtmlBefore($observer)
    {
        $paymentFormBlock = $observer->getEvent()->getBlock();
        $method = $paymentFormBlock->getMethod();

        if ($method && $method->getIsCentinelValidationEnabled()) {
            $paymentFormBlock->setChild(
               'payment.method.' . $method->getCode() . 'centinel.logo',
                Mage::helper('Magento_Centinel_Helper_Data')->getMethodFormBlock($method)
            );
        }
        return $this;
    }

    /**
     * Reset validation data
     *
     * @param \Magento\Object $observer
     * @return Magento_Centinel_Model_Observer
     */
    public function checkoutSubmitAllAfter($observer)
    {
        $method = false;

        if ($order = $observer->getEvent()->getOrder()) {
            $method = $order->getPayment()->getMethodInstance();
        } elseif ($orders = $observer->getEvent()->getOrders()) {
            if ($order = array_shift($orders)) {
                $method = $order->getPayment()->getMethodInstance();
            }
        }

        if ($method && $method->getIsCentinelValidationEnabled()) {
            $method->getCentinelValidator()->reset();
        }
        return $this;
    }
}

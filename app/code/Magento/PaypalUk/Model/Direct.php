<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PaypalUk
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPalUk Direct Module
 */
namespace Magento\PaypalUk\Model;

class Direct extends \Magento\Paypal\Model\Direct
{
    protected $_code  = \Magento\Paypal\Model\Config::METHOD_WPP_PE_DIRECT;

    /**
     * Website Payments Pro instance type
     *
     * @var string
     */
    protected $_proType = 'Magento\PaypalUk\Model\Pro';

    /**
     * Return available CC types for gateway based on merchant country
     *
     * @return string
     */
    public function getAllowedCcTypes()
    {
        return $this->_pro->getConfig()->cctypes;
    }

    /**
     * Merchant country limitation for 3d secure feature, rewrite for parent implementation
     *
     * @return bool
     */
    public function getIsCentinelValidationEnabled()
    {
        if (!parent::getIsCentinelValidationEnabled()) {
            return false;
        }
        // available only for US and UK merchants
        if (in_array($this->_pro->getConfig()->getMerchantCountry(), array('US', 'GB'))) {
            return true;
        }
        return false;
    }

    /**
     * Import direct payment results to payment
     *
     * @param \Magento\Paypal\Model\Api\Nvp
     * @param \Magento\Sales\Model\Order\Payment
     */
    protected function _importResultToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())->setIsTransactionClosed(0)
            ->setIsTransactionPending($api->getIsPaymentPending())
            ->setTransactionAdditionalInfo(\Magento\PaypalUk\Model\Pro::TRANSPORT_PAYFLOW_TXN_ID, $api->getTransactionId())
            ;
        $payment->setPreparedMessage(__('Payflow PNREF: #%1.', $api->getTransactionId()));
        $this->_pro->importPaymentInfo($api, $payment);
    }

    /**
     * Format credit card expiration date based on month and year values
     * Format: mmyy
     *
     * @param string|int $month
     * @param string|int $year
     * @return string
     */
    protected function _getFormattedCcExpirationDate($month, $year)
    {
        return sprintf('%02d', $month) . sprintf('%02d', substr($year, -2, 2));
    }
}

<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paygate data helper
 */
class Mage_Paygate_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Convert s lot of messages to one message
     *
     * @param  array $messages
     * @return string
     */
    public function messagesToMessage($messages)
    {
        return implode(' | ', $messages);
    }

    /**
     * Return message for preauthorize capture gateway action
     *
     * @param  Mage_Payment_Model_Info $payment
     * @param  Varien_Object $card
     * @param  string $lastTransactionId
     * @return string
     */
    public function getPlaceTransactionMessage($payment, $card, $transactionType)
    {
        switch ($transactionType) {
            case Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH:
                return $this->__(
                    'Credit Card: xxxx-%s Authorized amount of %s. Authorize.Net Transaction ID %s',
                    $card->getCcLast4(),
                    $this->_formatPrice($payment, $card->getProcessedAmount()),
                    $card->getLastTransId()
                );
            case Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE:
                return $this->__(
                    'Credit Card: xxxx-%s Authorized and Captured amount of %s. Authorize.Net Transaction ID %s',
                    $card->getCcLast4(),
                    $this->_formatPrice($payment, $card->getProcessedAmount()),
                    $card->getLastTransId()
                );
        }
    }

    /**
     * Return message for preauthorize capture gateway action
     *
     * @param  Mage_Payment_Model_Info $payment
     * @param  Varien_Object $card
     * @param  float $amount
     * @param  string $lastTransactionId
     * @param  Exception $exception
     * @return string
     */
    public function getPreauthorizeCaptureTransactionMessage($payment, $card, $amount, $lastTransactionId, $exception = null)
    {
        if (is_null($exception)) {
            return $this->__(
                'Credit Card: xxxx-%s Captured amount of %s. Authorize.Net Transaction ID %s - successful',
                $card->getCcLast4(),
                $this->_formatPrice($payment, $amount),
                $lastTransactionId
            );
        } else {
            return $this->__(
                'Credit Card: xxxx-%s Captured amount of %s. Authorize.Net Transaction ID %s - failed. %s',
                $card->getCcLast4(),
                $this->_formatPrice($payment, $amount),
                $lastTransactionId,
                $exception->getMessage()
            );
        }
    }

    /**
     * Return message for refund gateway action
     *
     * @param  Mage_Payment_Model_Info $payment
     * @param  Varien_Object $card
     * @param  float $amount
     * @param  string $lastTransactionId
     * @param  Exception $exception
     * @return string
     */
    public function getRefundTransactionMessage($payment, $card, $amount, $lastTransactionId, $exception = null)
    {
        if (is_null($exception)) {
            return $this->__(
                'Credit Card: xxxx-%s Refunded amount of %s. Authorize.Net Transaction ID %s - successful',
                $card->getCcLast4(),
                $this->_formatPrice($payment, $amount),
                $lastTransactionId
            );
        } else {
            return $this->__(
                'Credit Card: xxxx-%s Captured amount of %s. Authorize.Net Transaction ID %s - failed. %s',
                $card->getCcLast4(),
                $this->_formatPrice($payment, $amount),
                $lastTransactionId,
                $exception->getMessage()
            );
        }
    }

    /**
     * Return message for void gateway action
     *
     * @param  Mage_Payment_Model_Info $payment
     * @param  Varien_Object $card
     * @param  string $lastTransactionId
     * @param  Exception $exception
     * @return string
     */
    public function getVoidTransactionMessage($payment, $card, $lastTransactionId, $exception = null)
    {
        if (is_null($exception)) {
            return $this->__(
                'Credit Card: xxxx-%s Voided. Authorize.Net Transaction ID %s - successful',
                $card->getCcLast4(),
                $lastTransactionId
            );
        } else {
            return $this->__(
                'Credit Card: xxxx-%s Voided. Authorize.Net Transaction ID %s - failed. %s',
                $card->getCcLast4(),
                $lastTransactionId,
                $exception->getMessage()
            );
        }
    }

    /**
     * Format price with currency sign
     * @param  Mage_Payment_Model_Info $payment
     * @param float $amount
     * @return string
     */
    protected function _formatPrice($payment, $amount)
    {
        return $payment->getOrder()->getBaseCurrency()->formatTxt($amount);
    }
}

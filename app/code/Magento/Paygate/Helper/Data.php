<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paygate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paygate data helper
 */
class Magento_Paygate_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Converts a lot of messages to message
     *
     * @param  array $messages
     * @return string
     */
    public function convertMessagesToMessage($messages)
    {
        return implode(' | ', $messages);
    }

    /**
     * Return message for gateway transaction request
     *
     * @param  Magento_Payment_Model_Info $payment
     * @param  string $requestType
     * @param  string $lastTransactionId
     * @param  Magento_Object $card
     * @param float $amount
     * @param string $exception
     * @return bool|string
     */
    public function getTransactionMessage($payment, $requestType, $lastTransactionId, $card, $amount = false,
        $exception = false
    ) {
        return $this->getExtendedTransactionMessage(
            $payment, $requestType, $lastTransactionId, $card, $amount, $exception
        );
    }

    /**
     * Return message for gateway transaction request
     *
     * @param  Magento_Payment_Model_Info $payment
     * @param  string $requestType
     * @param  string $lastTransactionId
     * @param  Magento_Object $card
     * @param float $amount
     * @param string $exception
     * @param string $additionalMessage Custom message, which will be added to the end of generated message
     * @return bool|string
     */
    public function getExtendedTransactionMessage($payment, $requestType, $lastTransactionId, $card, $amount = false,
        $exception = false, $additionalMessage = false
    ) {
        $operation = $this->_getOperation($requestType);

        if (!$operation) {
            return false;
        }

        if ($amount) {
            $amount = __('amount %1', $this->_formatPrice($payment, $amount));
        }

        if ($exception) {
            $result = __('failed');
        } else {
            $result = __('successful');
        }

        $card = __('Credit Card: xxxx-%1', $card->getCcLast4());

        $pattern = '%s %s %s - %s.';
        $texts = array($card, $amount, $operation, $result);

        if (!is_null($lastTransactionId)) {
            $pattern .= ' %s.';
            $texts[] = __('Authorize.Net Transaction ID %1', $lastTransactionId);
        }

        if ($additionalMessage) {
            $pattern .= ' %s.';
            $texts[] = $additionalMessage;
        }
        $pattern .= ' %s';
        $texts[] = $exception;

        return call_user_func_array('__', array_merge(array($pattern), $texts));
    }

    /**
     * Return operation name for request type
     *
     * @param  string $requestType
     * @return bool|string
     */
    protected function _getOperation($requestType)
    {
        switch ($requestType) {
            case Magento_Paygate_Model_Authorizenet::REQUEST_TYPE_AUTH_ONLY:
                return __('authorize');
            case Magento_Paygate_Model_Authorizenet::REQUEST_TYPE_AUTH_CAPTURE:
                return __('authorize and capture');
            case Magento_Paygate_Model_Authorizenet::REQUEST_TYPE_PRIOR_AUTH_CAPTURE:
                return __('capture');
            case Magento_Paygate_Model_Authorizenet::REQUEST_TYPE_CREDIT:
                return __('refund');
            case Magento_Paygate_Model_Authorizenet::REQUEST_TYPE_VOID:
                return __('void');
            default:
                return false;
        }
    }

    /**
     * Format price with currency sign
     * @param  Magento_Payment_Model_Info $payment
     * @param float $amount
     * @return string
     */
    protected function _formatPrice($payment, $amount)
    {
        return $payment->getOrder()->getBaseCurrency()->formatTxt($amount);
    }
}

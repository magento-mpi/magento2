<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * @method PaymentMethod create()
 */
class PaymentMethodBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Get payment method id
     *
     * @param int $value
     * @return int
     */
    public function setPaymentId($value)
    {
        return $this->_set(PaymentMethod::PAYMENT_ID, $value);
    }

    /**
     * Get purchase order number
     *
     * @param string $value
     * @return string|null
     */
    public function setPoNumber($value)
    {
        return $this->_set(PaymentMethod::PO_NUMBER, $value);
    }

    /**
     * Get payment method code
     *
     * @param string $value
     * @return string
     */
    public function setMethod($value)
    {
        return $this->_set(PaymentMethod::METHOD, $value);
    }
}

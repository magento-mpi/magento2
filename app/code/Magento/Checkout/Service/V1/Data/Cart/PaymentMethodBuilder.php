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
class PaymentMethodBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * Get purchase order number
     *
     * @param string $value
     * @return $this
     */
    public function setPoNumber($value)
    {
        return $this->_set(PaymentMethod::PO_NUMBER, $value);
    }

    /**
     * Get payment method code
     *
     * @param string $value
     * @return $this
     */
    public function setMethod($value)
    {
        return $this->_set(PaymentMethod::METHOD, $value);
    }

    /**
     * Get credit card owner
     *
     * @param string $value
     * @return $this
     */
    public function setCcOwner($value)
    {
        return $this->_set(PaymentMethod::CC_OWNER, $value);
    }

    /**
     * Get credit card number
     *
     * @param string $value
     * @return $this
     */
    public function setCcNumber($value)
    {
        return $this->_set(PaymentMethod::CC_NUMBER, $value);
    }

    /**
     * Get credit card type
     *
     * @param string $value
     * @return $this
     */
    public function setCcType($value)
    {
        return $this->_set(PaymentMethod::CC_TYPE, $value);
    }

    /**
     * Get credit card expiration year
     *
     * @param string $value
     * @return $this
     */
    public function setCcExpYear($value)
    {
        return $this->_set(PaymentMethod::CC_EXP_YEAR, $value);
    }

    /**
     * Get credit card expiration month
     *
     * @param string $value
     * @return $this
     */
    public function setCcExpMonth($value)
    {
        return $this->_set(PaymentMethod::CC_EXP_MONTH, $value);
    }

    /**
     * Set payment additional payment details
     *
     * @param string $value
     * @return $this
     */
    public function setPaymentDetails($value)
    {
        return $this->_set(PaymentMethod::PAYMENT_DETAILS, $value);
    }
}

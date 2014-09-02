<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data;

/**
 * @method PaymentMethod create()
 */
class PaymentMethodBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * Set payment method code
     *
     * @param string $value
     * @return $this
     */
    public function setCode($value)
    {
        return $this->_set(PaymentMethod::CODE, $value);
    }

    /**
     * Set payment method title
     *
     * @param string $value
     * @return $this
     */
    public function setTitle($value)
    {
        return $this->_set(PaymentMethod::TITLE, $value);
    }
}

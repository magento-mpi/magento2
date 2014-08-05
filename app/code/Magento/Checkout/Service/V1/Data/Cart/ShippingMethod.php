<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Quote shipping method data
 *
 * @codeCoverageIgnore
 */
class ShippingMethod extends \Magento\Framework\Service\Data\AbstractObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_CARRIER_CODE = 'carrier_code';

    const KEY_METHOD_CODE = 'method_code';

    const KEY_DESCRIPTION = 'description';

    const KEY_SHIPPING_AMOUNT = 'amount';
    /**#@-*/

    /**
     * Get carrier code
     *
     * @return string
     */
    public function getCarrierCode()
    {
        return $this->_get(self::KEY_CARRIER_CODE);
    }

    /**
     * Get shipping method code
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_get(self::KEY_METHOD_CODE);
    }

    /**
     * Get shipping description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::KEY_DESCRIPTION);
    }

    /**
     * Get shipping amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->_get(self::KEY_SHIPPING_AMOUNT);
    }
}

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
    const CARRIER_CODE = 'carrier_code';

    const METHOD_CODE = 'method_code';

    const DESCRIPTION = 'description';

    const SHIPPING_AMOUNT = 'amount';

    const BASE_SHIPPING_AMOUNT = 'base_amount';
    /**#@-*/

    /**
     * Get carrier code
     *
     * @return string
     */
    public function getCarrierCode()
    {
        return $this->_get(self::CARRIER_CODE);
    }

    /**
     * Get shipping method code
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_get(self::METHOD_CODE);
    }

    /**
     * Get shipping description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Get shipping amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->_get(self::SHIPPING_AMOUNT);
    }

    /**
     * Get base shipping amount
     *
     * @return float
     */
    public function getBaseAmount()
    {
        return $this->_get(self::BASE_SHIPPING_AMOUNT);
    }
}

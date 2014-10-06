<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data;

/**
 * @codeCoverageIgnore
 */
class PaymentMethod extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    const CODE = 'code';

    const TITLE = 'title';

    /**
     * Get payment method code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * Get payment method title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }
}

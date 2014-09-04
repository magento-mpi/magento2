<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Block\Checkout\Onepage\Payment;

use Mtf\Block\Form;

/**
 * Class Additional
 * Checkout store credit payment form
 */
class Additional extends Form
{
    /**
     * Fill the root form
     *
     * @param array $payment
     * @return $this
     */
    public function fillStoreCredit(array $payment)
    {
        $mapping = $this->dataMapping($payment);
        $this->_fill($mapping, null);

        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Block\Checkout\Payment;

use Mtf\Block\Form;

/**
 * Class Additional
 * Checkout reward payment form
 */
class Additional extends Form
{
    /**
     * Fill the reward form on onepage checkout
     *
     * @param array $payment
     * @return $this
     */
    public function fillReward(array $payment)
    {
        $mapping = $this->dataMapping($payment);
        $this->_fill($mapping);

        return $this;
    }
}

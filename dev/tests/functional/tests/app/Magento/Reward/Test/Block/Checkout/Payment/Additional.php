<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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

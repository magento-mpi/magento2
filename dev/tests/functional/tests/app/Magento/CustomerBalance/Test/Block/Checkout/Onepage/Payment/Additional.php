<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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

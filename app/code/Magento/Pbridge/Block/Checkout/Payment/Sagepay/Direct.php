<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Pbridge\Block\Checkout\Payment\Sagepay;

class Direct extends \Magento\Pbridge\Block\Checkout\Payment\AbstractPayment
{
    /**
     * Whether to include shopping cart items parameters in Payment Bridge source URL
     *
     * @var bool
     */
    protected $_sendCart = true;
}

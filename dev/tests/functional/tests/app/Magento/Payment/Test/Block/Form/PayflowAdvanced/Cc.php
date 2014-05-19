<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Test\Block\Form\PayflowAdvanced;

use Mtf\Block\Form;
use Mtf\Client\Element;

/**
 * Class Cc
 * Card Verification frame on OnePageCheckout order review step
 *
 */
class Cc extends Form
{
    /**
     * 'Pay Now' button
     *
     * @var string
     */
    protected $continue = '#btn_pay_cc';

    /**
     * Press "Continue" button
     */
    public function pressContinue()
    {
        $this->_rootElement->find($this->continue)->click();
    }
}

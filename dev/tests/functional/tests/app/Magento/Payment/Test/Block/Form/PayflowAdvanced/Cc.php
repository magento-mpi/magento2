<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Test\Block\Form\PayflowAdvanced;

use Mtf\Fixture;
use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Block\Block;


/**
 * Class Cc
 * Card Verification frame on OnePageCheckout order review step
 *
 * @package Magento\Payment
 */
class Cc extends Form
{
    /**
     * Payment method code
     *
     * @var string
     */
    private $paymentCode = '';

    protected $continue = '#btn_pay_cc';

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Initialize mapping
        $this->_mapping = array(
            'credit_card_number' => '#cc_number',
            'expiration_month' => '#expdate_month',
            'expiration_year' => '#expdate_year',
            'credit_card_cvv' => '#cvv2_number',
        );
    }

    /**
     * Fill credit card form
     *
     * @param Fixture $fixture
     * @param Element $element
     */
    public function fill(Fixture $fixture, Element $element = null)
    {
        /** @var $fixture \Magento\Checkout\Test\Fixture\Checkout */
        parent::fill($fixture->getCreditCard(), $element);
    }

    /**
     * Press "Continue" button
     */
    public function pressContinue()
    {
        $this->_rootElement->find($this->continue)->click();
    }
}

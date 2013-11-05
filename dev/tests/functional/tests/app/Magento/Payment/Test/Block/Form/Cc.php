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

namespace Magento\Payment\Test\Block\Form;

use Mtf\Fixture;
use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Cc
 * Form for filling credit card data
 *
 * @package Magento\Payment\Test\Block\Form
 */
class Cc extends Form
{
    /**
     * Payment method code
     *
     * @var string
     */
    private $paymentCode = '';

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Initialize mapping
        $this->_mapping = array(
            'credit_card_type' => '#' . $this->paymentCode . '_cc_type',
            'credit_card_number' => '#' . $this->paymentCode . '_cc_number',
            'expiration_month' => '#' . $this->paymentCode . '_expiration',
            'expiration_year' => '#' . $this->paymentCode . '_expiration_yr',
            'credit_card_cvv' => '#' . $this->paymentCode . '_cc_cid',
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
        $this->paymentCode = $fixture->getPaymentMethod()->getPaymentCode();
        $this->_init();
        parent::fill($fixture->getCreditCard(), $element);
    }
}

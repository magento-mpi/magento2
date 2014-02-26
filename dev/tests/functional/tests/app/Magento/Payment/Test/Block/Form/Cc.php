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

use Mtf\Fixture\FixtureInterface;
use Mtf\Block\Form;
use Mtf\Client\Element;

/**
 * Class Cc
 * Form for filling credit card data
 *
 * @package Magento\Payment\Test\Block\Form
 */
class Cc extends Form
{
    /**
     * {@inheritdoc}
     */
    protected $_mapping = array(
        'credit_card_type' => '_cc_type',
        'credit_card_number' => '_cc_number',
        'expiration_month' => '_expiration',
        'expiration_year' => '_expiration_yr',
        'credit_card_cvv' => '_cc_cid',
    );

    /**
     * Fill credit card form
     *
     * @param FixtureInterface $fixture
     * @param Element $element
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        /** @var $fixture \Magento\Checkout\Test\Fixture\Checkout */
        $paymentCode = $fixture->getPaymentMethod()->getPaymentCode();
        foreach ($this->_mapping as $key => $value) {
            $this->_mapping[$key] = '#' . $paymentCode . $this->_mapping[$key];
        }
        parent::fill($fixture->getCreditCard(), $element);
    }
}

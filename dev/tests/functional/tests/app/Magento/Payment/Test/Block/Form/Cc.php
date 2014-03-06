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
     * Fill credit card form
     *
     * @param FixtureInterface $fixture
     * @param Element $element
     * @return $this|void
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        /** @var $fixture \Magento\Checkout\Test\Fixture\Checkout */
        $this->placeholders = ['paymentCode' => $fixture->getPaymentMethod()->getPaymentCode()];
        $this->applyPlaceholders();
        return parent::fill($fixture->getCreditCard(), $element);
    }
}

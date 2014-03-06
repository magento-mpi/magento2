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

namespace Magento\Checkout\Test\Block\Onepage;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Shipping
 * One page checkout status shipping block
 *
 * @package Magento\Checkout\Test\Block\Onepage
 */
class Shipping extends Form
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    protected $continue = '#shipping-buttons-container button';

    /**
     * Fill form data. Unset 'email' field as it absent in current form
     *
     * @param array $fields
     * @param Element $element
     */
    protected function _fill(array $fields, Element $element = null)
    {
        unset($fields['email']);
        parent::_fill($fields, $element);
    }

    /**
     * Fill shipping address
     *
     * @param Checkout $fixture
     */
    public function fillShipping(Checkout $fixture)
    {
        $shippingAddress = $fixture->getShippingAddress();
        if (!$shippingAddress) {
            return;
        }
        $this->fill($shippingAddress);
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('#shipping-please-wait');
    }
}

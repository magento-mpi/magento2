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

namespace Magento\Centinel\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;;


/**
 * Class CardVerification
 * Card Verification frame on OnePageCheckout order review step
 *
 * @package Magento\Centinel
 */
class CardVerification extends Block
{
    /**
     * Submit form button
     *
     * @var string
     */
    protected $submit = '[name="UsernamePasswordEntry"]';

    /**
     * Password input field
     *
     * @var string
     */
    protected $password = '[name="external.field.password"]';

    /**
     * Fill in and submit verification form
     *
     * @param Checkout $fixture
     */
    public function verifyCard(Checkout $fixture)
    {
        $data = $fixture->getCreditCard()->getValidationData();
        $this->waitForElementVisible($this->password);
        $this->_rootElement->find($this->password, Locator::SELECTOR_CSS)->setValue($data['password']);
        $this->_rootElement->find($this->submit, Locator::SELECTOR_CSS)->click();
    }
}

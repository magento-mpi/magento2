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
use Magento\Checkout\Test\Fixture\Checkout;


/**
 * Class Authentication
 * Card Verification frame on OnePageCheckout order review step
 *
 * @package Magento\Centinel
 */
class Authentication extends Block
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
        $data = $fixture->getCreditCard()->getValidationPassword();
        $this->waitForElementVisible($this->password);
        $this->_rootElement->find($this->password, Locator::SELECTOR_CSS)->setValue($data);
        $this->_rootElement->find($this->submit, Locator::SELECTOR_CSS)->click();
        //Workaround for https\http data transfer browser alert
        try {
            $this->_rootElement->acceptAlert();
        } catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e){
        }
    }

    /**
     * Get Text
     *
     * @return string
     */
    public function getText()
    {
        return $this->_rootElement->getText();
    }
}

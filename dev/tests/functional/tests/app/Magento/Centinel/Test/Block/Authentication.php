<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Centinel\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Client\Element;
use Mtf\Block\BlockFactory;

/**
 * Class Authentication
 * Card Verification frame on OnePageCheckout order review step
 *
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
     * 3D Secure frame locator
     *
     * @var string
     */
    protected $centinelFrame = '#centinel-authenticate-iframe';

    /**
     * @constructor
     * @param Element $element
     * @param BlockFactory $blockFactory
     * @param Browser $browser
     */
    public function __construct(Element $element, BlockFactory $blockFactory, Browser $browser)
    {
        parent::__construct($element, $blockFactory, $browser);
        $this->browser->switchToFrame(new Locator($this->centinelFrame));
    }

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
        } catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
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

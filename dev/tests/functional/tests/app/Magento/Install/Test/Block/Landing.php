<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Landing block.
 */
class Landing extends Block
{
    /**
     * 'Agree and Set up Magento' button.
     *
     * @var string
     */
    protected $agreeAndSetup = '//*[@class="btn-lg btn-primary"]';

    /**
     * 'Terms & Agreement' link.
     *
     * @var string
     */
    protected $termsAndAgreement = "//*[text()[contains(.,'Terms & Agreement')]]";

    /**
     * Click on 'Agree and Set up Magento' button.
     *
     * @return void
     */
    public function clickAgreeAndSetup()
    {
        $this->_rootElement->find($this->agreeAndSetup, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Click on 'Terms & Agreement' link.
     *
     * @return void
     */
    public function clickTermsAndAgreement()
    {
        $this->_rootElement->find($this->termsAndAgreement, Locator::SELECTOR_XPATH)->click();
    }
}
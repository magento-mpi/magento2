<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Test\Block\Form\PayflowAdvanced;

use Mtf\Client\Element;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Block\BlockFactory;
use Mtf\Client\Element\Locator;
use Mtf\Block\Mapper;
use Magento\Payment\Test\Block\Form\Cc;

/**
 * Class CcLink
 * Card Verification frame block
 */
class CcLink extends Cc
{
    /**
     * 'Pay Now' button
     *
     * @var string
     */
    protected $continue = '#btn_pay_cc';

    /**
     * Payflow Link iFrame locator
     *
     * @var string
     */
    protected $payflowLinkFrame = "#payflow-link-iframe";

    /**
     * @param Element $element
     * @param BlockFactory $blockFactory
     * @param Mapper $mapper
     * @param Browser $browser
     */
    public function __construct(Element $element, BlockFactory $blockFactory, Mapper $mapper, Browser $browser)
    {
        parent::__construct($element, $blockFactory, $mapper, $browser);
        $this->browser->switchToFrame(new Locator($this->payflowLinkFrame));
    }

    /**
     * Press "Continue" button
     *
     * @return void
     */
    public function pressContinue()
    {
        $this->_rootElement->find($this->continue)->click();
    }
}

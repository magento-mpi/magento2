<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Test\Block\Form\PayflowAdvanced;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Block\BlockFactory;
use Mtf\Client\Element\Locator;
use Mtf\Block\Mapper;

/**
 * Class CcLink
 * Card Verification frame on OnePageCheckout order review step
 *
 */
class CcLink extends Form
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
    public function __construct(Element $element, BlockFactory $blockFactory, Mapper $mapper, Browser $browser){
        parent::__construct($element, $blockFactory, $mapper, $browser);
        $this->browser->switchToFrame(new Locator($this->payflowLinkFrame));
    }
    /**
     * Press "Continue" button
     */
    public function pressContinue()
    {
        $this->_rootElement->find($this->continue)->click();
    }
}

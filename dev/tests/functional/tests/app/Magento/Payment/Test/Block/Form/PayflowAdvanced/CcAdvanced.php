<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Test\Block\Form\PayflowAdvanced;

use Mtf\Block\Form;
use Mtf\Block\Mapper;
use Mtf\Client\Element;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Block\BlockFactory;
use Mtf\Client\Element\Locator;

/**
 * Class CcAdvanced
 * Card Verification frame block
 */
class CcAdvanced extends Form
{
    /**
     * 'Pay Now' button
     *
     * @var string
     */
    protected $continue = '#btn_pay_cc';

    /**
     * Payflow Advanced iFrame locator
     *
     * @var string
     */
    protected $payflowAdvancedFrame = "#payflow-advanced-iframe";

    /**
     * @constructor
     * @param Element $element
     * @param BlockFactory $blockFactory
     * @param Mapper $mapper
     * @param Browser $browser
     */
    public function __construct(Element $element, BlockFactory $blockFactory, Mapper $mapper, Browser $browser)
    {
        parent::__construct($element, $blockFactory, $mapper, $browser);
        $this->browser->switchToFrame(new Locator($this->payflowAdvancedFrame));
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

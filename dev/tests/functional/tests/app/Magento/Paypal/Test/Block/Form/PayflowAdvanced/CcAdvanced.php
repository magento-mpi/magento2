<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Paypal\Test\Block\Form\PayflowAdvanced;

use Magento\Payment\Test\Block\Form\Cc;
use Mtf\Block\BlockFactory;
use Mtf\Block\Mapper;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class CcAdvanced
 * Card Verification frame block
 */
class CcAdvanced extends Cc
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

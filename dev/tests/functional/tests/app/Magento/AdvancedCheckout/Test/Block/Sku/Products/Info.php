<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Block\Sku\Products;

use Mtf\Block\Block;

/**
 * Class Info
 * SKU failed information Block
 */
class Info extends Block
{
    /**
     * Error message selector
     *
     * @var string
     */
    protected $errorMessage = '.message.error div';

    /**
     * Specify products options link selector
     *
     * @var string
     */
    protected $optionsLink = 'a.configure-popup';

    /**
     * Tier price message selector
     *
     * @var string
     */
    protected $tierPriceMessage = '.prices-tier .item';

    /**
     * MSRP notice selector
     *
     * @var string
     */
    protected $msrp = '.pricing.msrp';

    /**
     * Get error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_rootElement->find($this->errorMessage)->getText();
    }

    /**
     * Check that specify the product's options link is visible
     *
     * @return bool
     */
    public function linkIsVisible()
    {
        return $this->_rootElement->find($this->optionsLink)->isVisible();
    }

    /**
     * Click specify the product's options link
     *
     * @return void
     */
    public function clickOptionsLink()
    {
        $this->_rootElement->find($this->optionsLink)->click();
    }

    /**
     *  Get tier price messages
     *
     * @return array
     */
    public function getTierPriceMessages()
    {
        $messages = [];
        $elements = $this->_rootElement->find($this->tierPriceMessage)->getElements();
        foreach ($elements as $key => $element) {
            $messages[$key] = $element->getText();
        }

        return $messages;
    }

    /**
     * Check that MSRP notice displayed
     *
     * @return bool
     */
    public function isMsrpNoticeDisplayed()
    {
        return $this->_rootElement->find($this->msrp)->isVisible();
    }
}

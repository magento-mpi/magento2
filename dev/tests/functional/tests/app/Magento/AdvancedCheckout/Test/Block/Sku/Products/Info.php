<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
    protected $optionsLink = '.action.configure';

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
    protected $msrp = '.msrp.notice';

    /**
     * Delete button selector
     *
     * @var string
     */
    protected $deleteButton = '.action.delete';

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
     * Get tier price messages
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

    /**
     * Click delete button
     *
     * @return void
     */
    public function deleteProduct()
    {
        $this->_rootElement->find($this->deleteButton)->click();
    }
}

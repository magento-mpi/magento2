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
 * Order by SKU form
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
     * Specify products options link  selector
     *
     * @var string
     */
    protected $optionsLink = 'a.configure-popup';

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
     * Check that "Specify the product's options" link is visible
     *
     * @return bool
     */
    public function linkIsVisible()
    {
        return $this->_rootElement->find($this->optionsLink)->isVisible();
    }

    /**
     * Click "Specify the product's options" link
     *
     * @return void
     */
    public function clickOptionsLink()
    {
        $this->_rootElement->find($this->optionsLink)->click();
    }
}

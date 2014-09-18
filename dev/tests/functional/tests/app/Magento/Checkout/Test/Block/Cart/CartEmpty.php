<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Checkout\Test\Block\Cart;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class CartEmpty
 * Block for text of empty cart
 */
class CartEmpty extends Block
{
    /**
     * Selector for link "here" to main page
     *
     * @var string
    */
    protected $linkToMainPage = './/a[.="here"]';

    /**
     * Get test for empty cart
     *
     * @return string
     */
    public function getText()
    {
        return str_replace("\n", ' ', $this->_rootElement->getText());
    }

    /**
     * Click link to main page
     *
     * @return void
     */
    public function clickLinkToMainPage()
    {
        $this->_rootElement->find($this->linkToMainPage, Locator::SELECTOR_XPATH)->click();
    }
}

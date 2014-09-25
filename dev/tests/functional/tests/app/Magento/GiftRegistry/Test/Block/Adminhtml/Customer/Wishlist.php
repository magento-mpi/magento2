<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Customer;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Wishlist
 * Gift registry wishlist main block
 */
class Wishlist extends Block
{
    /**
     * Gift registry drop down selector
     *
     * @var string
     */
    protected $addGiftRegistry = '.giftregisty.add span.action';

    /**
     * Gift registry selector
     *
     * @var string
     */
    protected $giftRegistry = '//ul[contains(@class, "item")]/li[contains(text(), "%s")]';

    /**
     * Click to save button
     *
     * @param string $giftRegistry
     * @return void
     */
    public function addToGiftRegistry($giftRegistry)
    {
        $this->openGiftRegistry();
        $this->_rootElement->find(sprintf($this->giftRegistry, $giftRegistry), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Check that gift registry visible in wishlist
     *
     * @param string $giftRegistry
     * @return bool
     */
    public function giftRegistryIsVisible($giftRegistry)
    {
        $this->openGiftRegistry();
        $selector = sprintf($this->giftRegistry, $giftRegistry);
        return $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Open gift registry drop down
     *
     * @return void
     */
    protected function openGiftRegistry()
    {
        $this->_rootElement->find($this->addGiftRegistry)->click();
    }
}

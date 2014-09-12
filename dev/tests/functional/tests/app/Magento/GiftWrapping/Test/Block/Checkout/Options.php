<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Block\Checkout;

use Mtf\Block\Block;
use Magento\GiftWrapping\Test\Fixture\GiftWrapping;

/**
 * Class Options
 * Gift options block on shipping method step on one page checkout frontend
 */
class Options extends Block
{
    /**
     * Add gift options
     *
     * @var string
     */
    protected $allowGiftOptions = 'input[name="allow_gift_options"]';

    /**
     * Gift Options for individual items
     *
     * @var string
     */
    protected $allowGiftOptionsForItems = 'input[name="allow_gift_options_for_items"]';

    /**
     * Gift Wrapping Design
     *
     * @var string
     */
    protected $giftWrapping = 'select[name$="[design]"]';

    /**
     * Check if Gift Wrapping Design Available on Onepage Checkout
     *
     * @param GiftWrapping $giftWrapping
     * @return bool
     */
    public function isGiftWrappingAvailable(GiftWrapping $giftWrapping)
    {
        $this->_rootElement->find($this->allowGiftOptions)->click();
        $this->_rootElement->find($this->allowGiftOptionsForItems)->click();
        if ($this->_rootElement->find($this->giftWrapping)->isVisible()) {
            return strpos($this->_rootElement->find($this->giftWrapping)->getText(), $giftWrapping->getDesign());
        } else {
            return false;
        }
    }
}

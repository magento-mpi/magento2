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
     * Gift Wrapping Design Options
     *
     * @var string
     */
    protected $giftWrappingOptions = 'select[name$="[design]"] > option';

    /**
     * Get Gift Wrappings Available on Onepage Checkout
     *
     * @return array
     */
    public function getGiftWrappingsAvailable()
    {
        $this->_rootElement->find($this->allowGiftOptions)->click();
        $this->_rootElement->find($this->allowGiftOptionsForItems)->click();
        $giftWrappings = $this->_rootElement->find($this->giftWrappingOptions)->getElements();
        $getGiftWrappingsAvailable = [];
        foreach ($giftWrappings as $giftWrapping) {
            $getGiftWrappingsAvailable[] = $giftWrapping->getText();
        }

        return $getGiftWrappingsAvailable;
    }
}

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
     * Get Gift Wrapping Available on Onepage Checkout
     *
     * @param array $giftWrappings
     * @return array
     */
    public function getGiftWrappingAvailable(array $giftWrappings)
    {
        $this->_rootElement->find($this->allowGiftOptions)->click();
        $this->_rootElement->find($this->allowGiftOptionsForItems)->click();
        $giftWrappingElements = $this->_rootElement->find($this->giftWrappingOptions)->getElements();
        $matches = [];
        foreach ($giftWrappings as $giftWrapping) {
            foreach ($giftWrappingElements as $giftWrappingElement) {
                if ($giftWrapping->getDesign() === $giftWrappingElement->getText()) {
                    $matches[] = $giftWrapping->getDesign();
                }
            }
        }

        return $matches;
    }
}

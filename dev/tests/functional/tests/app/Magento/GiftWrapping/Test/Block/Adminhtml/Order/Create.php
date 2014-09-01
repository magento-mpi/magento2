<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Block\Adminhtml\Order;

use Mtf\Block\Block;

/**
 * Class Create
 * Adminhtml Gift Wrapping order create block
 */
class Create extends Block
{
    /**
     * Gift Wrapping design block locator
     *
     * @var string
     */
    protected $giftWrappingDesignBlock = '#giftwrapping_design';

    /**
     * Check if Gift Wrapping design is available on order creation page
     *
     * @param string $giftWrappingDesign
     * @return bool
     */
    public function isGiftWrappingAvailable($giftWrappingDesign)
    {
        $giftWrappings = $this->_rootElement->find($this->giftWrappingDesignBlock)->getText();
        return strpos($giftWrappings, $giftWrappingDesign);
    }
}

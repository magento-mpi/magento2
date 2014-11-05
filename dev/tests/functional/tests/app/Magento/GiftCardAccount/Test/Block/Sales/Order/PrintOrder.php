<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Block\Sales\Order;

/**
 * Class PrintOrder
 * Print Order block
 */
class PrintOrder extends \Magento\Sales\Test\Block\Order\PrintOrder
{
    /**
     * Gift card selector.
     *
     * @var string
     */
    protected $giftCardsSelector = '.giftcard.totals';

    /**
     * Returns gift card block on print order page.
     *
     * @return \Magento\GiftCardAccount\Test\Block\Sales\Order\PrintOrder\GiftCards
     */
    public function getGiftCardsBlock()
    {
        $giftCardsBlock = $this->blockFactory->create(
            'Magento\GiftCardAccount\Test\Block\Sales\Order\PrintOrder\GiftCards',
            ['element' => $this->_rootElement->find($this->giftCardsSelector)]
        );

        return $giftCardsBlock;
    }
}

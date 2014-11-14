<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Service\V1\Data\Cart;

/**
 * Gift Card Account data for quote
 *
 * @codeCoverageIgnore
 */
class GiftCardAccount extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array
     */
    const GIFT_CARDS = 'gift_cards';

    const GIFT_CARDS_AMOUNT = 'gift_cards_amount';

    const BASE_GIFT_CARDS_AMOUNT = 'base_gift_cards_amount';

    const GIFT_CARDS_AMOUNT_USED = 'gift_cards_amount_used';

    const BASE_GIFT_CARDS_AMOUNT_USED = 'base_gift_cards_amount_used';

    /**#@-*/

    /**
     * Gift cards codes
     *
     * @return string[]
     */
    public function getGiftCards()
    {
        return $this->_get(self::GIFT_CARDS);
    }

    /**
     * Gift cards amount in quote currency
     *
     * @return int|null
     */
    public function getGiftCardsAmount()
    {
        return $this->_get(self::GIFT_CARDS_AMOUNT);
    }

    /**
     * Gift cards amount in base currency
     *
     * @return int|null
     */
    public function getBaseGiftCardsAmount()
    {
        return $this->_get(self::BASE_GIFT_CARDS_AMOUNT);
    }

    /**
     * Gift cards amount used in quote currency
     *
     * @return int|null
     */
    public function getGiftCardsAmountUsed()
    {
        return $this->_get(self::GIFT_CARDS_AMOUNT_USED);
    }

    /**
     * Gift cards amount used in base currency
     *
     * @return int|null
     */
    public function getBaseGiftCardsAmountUsed()
    {
        return $this->_get(self::BASE_GIFT_CARDS_AMOUNT_USED);
    }
}

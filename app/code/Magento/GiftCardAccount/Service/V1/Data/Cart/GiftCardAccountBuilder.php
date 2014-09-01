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
class GiftCardAccountBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * Constants defined for keys of array
     */
    const GIFT_CARDS = 'gift_cards';

    const GIFT_CARDS_AMOUNT = 'gift_cards_amount';

    const BASE_GIFT_CARDS_AMOUNT = 'base_gift_cards_amount';

    const GIFT_CARDS_AMOUNT_USED = 'gift_cards_amount_used';

    const BASE_GIFT_CARDS_AMOUNT_USED = 'base_gift_cards_amount_used';

    /**
     * Set Gift cards codes
     *
     * @param string[] $value
     * @return $this
     */
    public function setGiftCards($value)
    {
        return $this->_set(GiftCardAccount::GIFT_CARDS, $value);
    }

    /**
     * Set Gift cards amount in quote currency
     *
     * @param int|null $value
     * @return $this
     */
    public function setGiftCardsAmount($value)
    {
        return $this->_set(GiftCardAccount::GIFT_CARDS_AMOUNT, $value);
    }

    /**
     * Set Gift card amount in base currency
     *
     * @param int|null $value
     * @return $this
     */
    public function setBaseGiftCardsAmount($value)
    {
        return $this->_set(GiftCardAccount::BASE_GIFT_CARDS_AMOUNT, $value);
    }

    /**
     * Set used gift card amount in quote currency
     *
     * @param int|null $value
     * @return $this
     */
    public function setGiftCardsAmountUsed($value)
    {
        return $this->_set(GiftCardAccount::GIFT_CARDS_AMOUNT_USED, $value);
    }

    /**
     * Set used gift card amount in base currency
     *
     * @param int|null $value
     * @return $this
     */
    public function setBaseGiftCardsAmountUsed($value)
    {
        return $this->_set(GiftCardAccount::BASE_GIFT_CARDS_AMOUNT_USED, $value);
    }
}

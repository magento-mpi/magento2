<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Maximal gift card code length according to database table definitions (longer codes are truncated)
     */
    const GIFT_CARD_CODE_MAX_LENGTH = 255;

    /**
     * Unserialize and return gift card list from specified object
     *
     * @param \Magento\Object $from
     * @return mixed
     */
    public function getCards(\Magento\Object $from)
    {
        $value = $from->getGiftCards();
        if (!$value) {
            return array();
        }
        return unserialize($value);
    }

    /**
     * Serialize and set gift card list to specified object
     *
     * @param \Magento\Object $to
     * @param mixed $value
     */
    public function setCards(\Magento\Object $to, $value)
    {
        $serializedValue = serialize($value);
        $to->setGiftCards($serializedValue);
    }
}

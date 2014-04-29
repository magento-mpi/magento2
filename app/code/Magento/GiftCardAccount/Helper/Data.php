<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Maximal gift card code length according to database table definitions (longer codes are truncated)
     */
    const GIFT_CARD_CODE_MAX_LENGTH = 255;

    /**
     * Unserialize and return gift card list from specified object
     *
     * @param \Magento\Framework\Object $from
     * @return mixed
     */
    public function getCards(\Magento\Framework\Object $from)
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
     * @param \Magento\Framework\Object $to
     * @param mixed $value
     * @return void
     */
    public function setCards(\Magento\Framework\Object $to, $value)
    {
        $serializedValue = serialize($value);
        $to->setGiftCards($serializedValue);
    }
}

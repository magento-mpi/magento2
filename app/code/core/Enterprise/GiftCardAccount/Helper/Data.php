<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Unserialize and return gift card list from specified object
     *
     * @param Varien_Object $from
     * @return mixed
     */
    public function getCards(Varien_Object $from)
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
     * @param Varien_Object $to
     * @param mixed $value
     */
    public function setCards(Varien_Object $to, $value)
    {
        $serializedValue = serialize($value);
        $to->setGiftCards($serializedValue);
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Giftcard module helper
 */
class Enterprise_GiftCard_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Instantiate giftardaccounts block when a gift card email should be sent
     * @return Mage_Core_Block_Template
     */
    public function getEmailGeneratedItemsBlock()
    {
        $className = Mage::getConfig()->getBlockClassName('Mage_Core_Block_Template');
        $block = new $className();
        $block->setTemplate('Enterprise_GiftCard::email/generated.phtml');
        return $block;
    }
}

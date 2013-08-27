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
class Enterprise_GiftCard_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Instantiate giftardaccounts block when a gift card email should be sent
     * @return Magento_Core_Block_Template
     */
    public function getEmailGeneratedItemsBlock()
    {
        $block = Mage::getObjectManager()->create('Magento_Core_Block_Template');
        $block->setTemplate('Enterprise_GiftCard::email/generated.phtml');
        return $block;
    }
}

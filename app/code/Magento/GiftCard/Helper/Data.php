<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Giftcard module helper
 */
class Magento_GiftCard_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Instantiate giftardaccounts block when a gift card email should be sent
     * @return Magento_Core_Block_Template
     */
    public function getEmailGeneratedItemsBlock()
    {
        /** @var $block Magento_Core_Block_Template */
        $block = $this->_layout->createBlock('Magento_Core_Block_Template');
        $block->setTemplate('Magento_GiftCard::email/generated.phtml');
        return $block;
    }
}

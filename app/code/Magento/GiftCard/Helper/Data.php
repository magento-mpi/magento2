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
namespace Magento\GiftCard\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Instantiate giftardaccounts block when a gift card email should be sent
     * @return \Magento\Core\Block\Template
     */
    public function getEmailGeneratedItemsBlock()
    {
        $block = \Mage::getObjectManager()->create('Magento\Core\Block\Template');
        $block->setTemplate('Magento_GiftCard::email/generated.phtml');
        return $block;
    }
}

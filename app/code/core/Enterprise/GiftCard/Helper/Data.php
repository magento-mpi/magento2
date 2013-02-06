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
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_BlockFactory $blockFactory
     */
    public function __construct(Mage_Core_Model_Translate $translator, Mage_Core_Model_BlockFactory $blockFactory)
    {
        parent::__construct($translator);
    }

    /**
     * Instantiate giftardaccounts block when a gift card email should be sent
     * @return Mage_Core_Block_Template
     */
    public function getEmailGeneratedItemsBlock()
    {
        $block = Mage::getObjectManager()->create('Mage_Core_Block_Template');
        $block->setTemplate('Enterprise_GiftCard::email/generated.phtml');
        return $block;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Check result block for a Giftcardaccount
 *
 */
class Enterprise_GiftCardAccount_Block_Check extends Magento_Core_Block_Template
{
    /**
     * Get current card instance from registry
     *
     * @return Enterprise_GiftCardAccount_Model_Giftcardaccount
     */
    public function getCard()
    {
        return Mage::registry('current_giftcardaccount');
    }

    /**
     * Check whether a gift card account code is provided in request
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getRequest()->getParam('giftcard-code', '');
    }
}

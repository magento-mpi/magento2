<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Block_Account_Redeem extends Magento_Core_Block_Template
{
    /**
     * Stub for future ability to implement redeem limitations based on customer/settings
     *
     * @return boold
     */
    public function canRedeem()
    {
        return Mage::helper('Enterprise_CustomerBalance_Helper_Data')->isEnabled();
    }

    /**
     * Retreive gift card code from url, empty if none
     *
     * @return string
     */
    public function getCurrentGiftcard()
    {
        $code = $this->getRequest()->getParam('giftcard', '');

        return $this->escapeHtml($code);
    }
}

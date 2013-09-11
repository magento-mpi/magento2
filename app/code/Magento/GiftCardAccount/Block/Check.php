<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Check result block for a Giftcardaccount
 *
 */
namespace Magento\GiftCardAccount\Block;

class Check extends \Magento\Core\Block\Template
{
    /**
     * Get current card instance from registry
     *
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    public function getCard()
    {
        return \Mage::registry('current_giftcardaccount');
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

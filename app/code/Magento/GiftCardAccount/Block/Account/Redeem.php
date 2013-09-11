<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Block\Account;

class Redeem extends \Magento\Core\Block\Template
{
    /**
     * Stub for future ability to implement redeem limitations based on customer/settings
     *
     * @return boold
     */
    public function canRedeem()
    {
        return \Mage::helper('Magento\CustomerBalance\Helper\Data')->isEnabled();
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

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
 * GiftCard Account api
 *
 * @category   Enterprise
 * @package    Enterprise_GiftCardAccount
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftCardAccount_Model_Api_V2 extends Enterprise_GiftCardAccount_Model_Api
{
    /**
     * Checks giftcard account data
     *
     * @throws Mage_Api_Exception
     * @param  stdClass $giftcardAccountData
     * @return array
     */
    protected function _prepareCreateGiftcardAccountData($giftcardAccountData)
    {
        if ($giftcardAccountData instanceof stdClass) {
            $giftcardAccountData = get_object_vars($giftcardAccountData);
        } else {
            $this->_fault('invalid_giftcardaccount_data');
        }
        return parent::_prepareCreateGiftcardAccountData($giftcardAccountData);
    }

    /**
     * Checks email notification data
     *
     * @throws Mage_Api_Exception
     * @param  null|stdClass $notificationData
     * @return array
     */
    protected function _prepareCreateNotificationData($notificationData = null)
    {
        if (isset($notificationData)) {
            if ($notificationData instanceof stdClass) {
                $notificationData = get_object_vars($notificationData);
            } else {
                $this->_fault('invalid_notification_data');
            }
        }
        return parent::_prepareCreateNotificationData($notificationData);
    }
}

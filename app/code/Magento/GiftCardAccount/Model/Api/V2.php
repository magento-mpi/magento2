<?php
/**
 * Gift card account API
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Model\Api;

class V2 extends \Magento\GiftCardAccount\Model\Api
{
    /**
     * Checks giftcard account data
     *
     * @throws \Magento\Api\Exception
     * @param  \stdClass $giftcardAccountData
     * @return array
     */
    protected function _prepareCreateGiftcardAccountData($giftcardAccountData)
    {
        if ($giftcardAccountData instanceof \stdClass) {
            $giftcardAccountData = get_object_vars($giftcardAccountData);
        } else {
            $this->_fault('invalid_giftcardaccount_data');
        }
        return parent::_prepareCreateGiftcardAccountData($giftcardAccountData);
    }

    /**
     * Checks email notification data
     *
     * @throws \Magento\Api\Exception
     * @param  null|stdClass $notificationData
     * @return array
     */
    protected function _prepareCreateNotificationData($notificationData = null)
    {
        if (isset($notificationData)) {
            if ($notificationData instanceof \stdClass) {
                $notificationData = get_object_vars($notificationData);
            } else {
                $this->_fault('invalid_notification_data');
            }
        }
        return parent::_prepareCreateNotificationData($notificationData);
    }
}

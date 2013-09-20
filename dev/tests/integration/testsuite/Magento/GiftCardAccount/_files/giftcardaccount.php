<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $model \Magento\GiftCardAccount\Model\Giftcardaccount */
$model = \Mage::getModel('Magento\GiftCardAccount\Model\Giftcardaccount');
$model->setCode('giftcardaccount_fixture')
    ->setStatus(\Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_ENABLED)
    ->setState(\Magento\GiftCardAccount\Model\Giftcardaccount::STATE_AVAILABLE)
    ->setWebsiteId(\Mage::app()->getWebsite()->getId())
    ->setIsRedeemable(\Magento\GiftCardAccount\Model\Giftcardaccount::REDEEMABLE)
    ->setBalance(9.99)
    ->setDateExpires(date('Y-m-d'))
    ->save()
;

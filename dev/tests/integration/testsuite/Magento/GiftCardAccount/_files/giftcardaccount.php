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

/** @var $model Magento_GiftCardAccount_Model_Giftcardaccount */
$model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_GiftCardAccount_Model_Giftcardaccount');
$model->setCode('giftcardaccount_fixture')
    ->setStatus(Magento_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED)
    ->setState(Magento_GiftCardAccount_Model_Giftcardaccount::STATE_AVAILABLE)
    ->setWebsiteId(
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_StoreManagerInterface')->getWebsite()->getId()
    )
    ->setIsRedeemable(Magento_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE)
    ->setBalance(9.99)
    ->setDateExpires(date('Y-m-d'))
    ->save()
;

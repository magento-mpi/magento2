<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $model Enterprise_GiftCardAccount_Model_Giftcardaccount */
$model = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount');
$model->setCode('giftcardaccount_fixture')
    ->setStatus(Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED)
    ->setState(Enterprise_GiftCardAccount_Model_Giftcardaccount::STATE_AVAILABLE)
    ->setWebsiteId(Mage::app()->getWebsite()->getId())
    ->setIsRedeemable(Enterprise_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE)
    ->setBalance(9.99)
    ->setDateExpires(date('Y-m-d'))
    ->save()
;

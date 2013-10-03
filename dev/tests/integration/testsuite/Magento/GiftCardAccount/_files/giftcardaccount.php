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
$model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\GiftCardAccount\Model\Giftcardaccount');
$model->setCode('giftcardaccount_fixture')
    ->setStatus(\Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_ENABLED)
    ->setState(\Magento\GiftCardAccount\Model\Giftcardaccount::STATE_AVAILABLE)
    ->setWebsiteId(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface')->getWebsite()->getId()
    )
    ->setIsRedeemable(\Magento\GiftCardAccount\Model\Giftcardaccount::REDEEMABLE)
    ->setBalance(9.99)
    ->setDateExpires(date('Y-m-d'))
    ->save()
;

<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var $model \Magento\GiftCardAccount\Model\Giftcardaccount */
$model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\GiftCardAccount\Model\Giftcardaccount'
);
$model->setCode(
    'giftcardaccount_fixture'
)->setStatus(
    \Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_ENABLED
)->setState(
    \Magento\GiftCardAccount\Model\Giftcardaccount::STATE_AVAILABLE
)->setWebsiteId(
    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        'Magento\Store\Model\StoreManagerInterface'
    )->getWebsite()->getId()
)->setIsRedeemable(
    \Magento\GiftCardAccount\Model\Giftcardaccount::REDEEMABLE
)->setBalance(
    9.99
)->setDateExpires(
    date('Y-m-d')
)->save();

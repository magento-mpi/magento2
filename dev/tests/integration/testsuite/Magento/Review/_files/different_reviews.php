<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';

$review = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Review\Model\Review',
    array('data' => array('nickname' => 'Nickname', 'title' => 'Review Summary', 'detail' => 'Review text'))
);
$review->setEntityId(
    $review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE)
)->setEntityPkValue(
    1
)->setStatusId(
    \Magento\Review\Model\Review::STATUS_PENDING
)->setStoreId(
    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        'Magento\Framework\StoreManagerInterface'
    )->getStore()->getId()
)->setStores(
    array(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\StoreManagerInterface'
        )->getStore()->getId()
    )
)->save();

$review = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Review\Model\Review',
    array('data' => array('nickname' => 'Nickname', 'title' => '2 filter first review', 'detail' => 'Review text'))
);
$review->setEntityId(
    $review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE)
)->setEntityPkValue(
    1
)->setStatusId(
    \Magento\Review\Model\Review::STATUS_APPROVED
)->setStoreId(
    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        'Magento\Framework\StoreManagerInterface'
    )->getStore()->getId()
)->setStores(
    array(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\StoreManagerInterface'
        )->getStore()->getId()
    )
)->save();

$review = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Review\Model\Review',
    array('data' => array('nickname' => 'Nickname', 'title' => '1 filter second review', 'detail' => 'Review text'))
);
$review->setEntityId(
    $review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE)
)->setEntityPkValue(
    1
)->setStatusId(
    \Magento\Review\Model\Review::STATUS_APPROVED
)->setStoreId(
    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        'Magento\Framework\StoreManagerInterface'
    )->getStore()->getId()
)->setStores(
    array(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\StoreManagerInterface'
        )->getStore()->getId()
    )
)->save();

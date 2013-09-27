<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';

$review = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Review\Model\Review', array(
    'data' => array(
        'nickname' => 'Nickname',
        'title'    => 'Review Summary',
        'detail'   => 'Review text'
    )
));
$review
    ->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
    ->setEntityPkValue(1) // the last product from the fixture file included above
    ->setStatusId(\Magento\Review\Model\Review::STATUS_PENDING)
    ->setStoreId(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->getId()
    )
    ->setStores(array(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->getId()
    ))
    ->save();

$review = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Review\Model\Review', array(
    'data' => array(
        'nickname' => 'Nickname',
        'title'    => '2 filter first review',
        'detail'   => 'Review text'
    )
));
$review
    ->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
    ->setEntityPkValue(1) // the last product from the fixture file included above
    ->setStatusId(\Magento\Review\Model\Review::STATUS_APPROVED)
    ->setStoreId(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->getId()
    )
    ->setStores(array(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->getId()
    ))
    ->save();

$review = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Review\Model\Review', array(
    'data' => array(
        'nickname' => 'Nickname',
        'title'    => '1 filter second review',
        'detail'   => 'Review text'
    )
));
$review
    ->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
    ->setEntityPkValue(1) // the last product from the fixture file included above
    ->setStatusId(\Magento\Review\Model\Review::STATUS_APPROVED)
    ->setStoreId(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->getId()
    )
    ->setStores(array(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->getId()
    ))
    ->save();

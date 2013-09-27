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

$review = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Review_Model_Review', array(
    'data' => array(
        'nickname' => 'Nickname',
        'title'    => 'Review Summary',
        'detail'   => 'Review text'
    )
));
$review
    ->setEntityId($review->getEntityIdByCode(Magento_Review_Model_Review::ENTITY_PRODUCT_CODE))
    ->setEntityPkValue(1) // the last product from the fixture file included above
    ->setStatusId(Magento_Review_Model_Review::STATUS_PENDING)
    ->setStoreId(
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->getId()
    )
    ->setStores(array(
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->getId()
    ))
    ->save();

$review = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Review_Model_Review', array(
    'data' => array(
        'nickname' => 'Nickname',
        'title'    => '2 filter first review',
        'detail'   => 'Review text'
    )
));
$review
    ->setEntityId($review->getEntityIdByCode(Magento_Review_Model_Review::ENTITY_PRODUCT_CODE))
    ->setEntityPkValue(1) // the last product from the fixture file included above
    ->setStatusId(Magento_Review_Model_Review::STATUS_APPROVED)
    ->setStoreId(
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->getId()
    )
    ->setStores(array(
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->getId()
    ))
    ->save();

$review = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Review_Model_Review', array(
    'data' => array(
        'nickname' => 'Nickname',
        'title'    => '1 filter second review',
        'detail'   => 'Review text'
    )
));
$review
    ->setEntityId($review->getEntityIdByCode(Magento_Review_Model_Review::ENTITY_PRODUCT_CODE))
    ->setEntityPkValue(1) // the last product from the fixture file included above
    ->setStatusId(Magento_Review_Model_Review::STATUS_APPROVED)
    ->setStoreId(
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->getId()
    )
    ->setStores(array(
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->getId()
    ))
    ->save();

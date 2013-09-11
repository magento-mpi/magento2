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

require __DIR__ . '/../../../Magento/Catalog/_files/multiple_products.php';

$review = Mage::getModel('Magento\Review\Model\Review',
    array('data' => array('nickname' => 'Nickname', 'title' => 'Review Summary', 'detail' => 'Review text'))
);
$review->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
    ->setEntityPkValue($product->getId()) // the last product from the fixture file included above
    ->setStatusId(\Magento\Review\Model\Review::STATUS_PENDING)
    ->setStoreId(Mage::app()->getStore()->getId())
    ->setStores(array(Mage::app()->getStore()->getId()))
    ->save()
;

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review data install
 *
 * @category    Magento
 * @package     Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

//Fill table review/review_entity
$reviewEntityCodes = array(
    Magento_Review_Model_Review::ENTITY_PRODUCT_CODE,
    Magento_Review_Model_Review::ENTITY_CUSTOMER_CODE,
    Magento_Review_Model_Review::ENTITY_CATEGORY_CODE,
);
foreach ($reviewEntityCodes as $entityCode) {
    $installer->getConnection()
            ->insert($installer->getTable('review_entity'), array('entity_code' => $entityCode));
}

//Fill table review/review_entity
$reviewStatuses = array(
    Magento_Review_Model_Review::STATUS_APPROVED       => 'Approved',
    Magento_Review_Model_Review::STATUS_PENDING        => 'Pending',
    Magento_Review_Model_Review::STATUS_NOT_APPROVED   => 'Not Approved'
);
foreach ($reviewStatuses as $k => $v) {
    $bind = array(
        'status_id'     => $k,
        'status_code'   => $v
    );
    $installer->getConnection()->insertForce($installer->getTable('review_status'), $bind);
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review data install
 *
 * @category    Mage
 * @package     Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

//Fill table review/review_entity
$reviewEntityCodes = array(
    Mage_Review_Model_Review::ENTITY_PRODUCT_CODE,
    Mage_Review_Model_Review::ENTITY_CUSTOMER_CODE,
    Mage_Review_Model_Review::ENTITY_CATEGORY_CODE,
);
foreach ($reviewEntityCodes as $entityCode) {
    $installer->getConnection()
            ->insert($installer->getTable('review_entity'), array('entity_code' => $entityCode));
}

//Fill table review/review_entity
$reviewStatuses = array(
    Mage_Review_Model_Review::STATUS_APPROVED       => 'Approved',
    Mage_Review_Model_Review::STATUS_PENDING        => 'Pending',
    Mage_Review_Model_Review::STATUS_NOT_APPROVED   => 'Not Approved'
);
foreach ($reviewStatuses as $k => $v) {
    $bind = array(
        'status_id'     => $k,
        'status_code'   => $v
    );
    $installer->getConnection()->insertForce($installer->getTable('review_status'), $bind);
}

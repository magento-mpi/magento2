<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review data install
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/* @var $installer \Magento\Framework\Module\DataSetup */
$installer = $this;

//Fill table review/review_entity
$reviewEntityCodes = array(
    \Magento\Review\Model\Review::ENTITY_PRODUCT_CODE,
    \Magento\Review\Model\Review::ENTITY_CUSTOMER_CODE,
    \Magento\Review\Model\Review::ENTITY_CATEGORY_CODE
);
foreach ($reviewEntityCodes as $entityCode) {
    $installer->getConnection()->insert($installer->getTable('review_entity'), array('entity_code' => $entityCode));
}

//Fill table review/review_entity
$reviewStatuses = array(
    \Magento\Review\Model\Review::STATUS_APPROVED => 'Approved',
    \Magento\Review\Model\Review::STATUS_PENDING => 'Pending',
    \Magento\Review\Model\Review::STATUS_NOT_APPROVED => 'Not Approved'
);
foreach ($reviewStatuses as $k => $v) {
    $bind = array('status_id' => $k, 'status_code' => $v);
    $installer->getConnection()->insertForce($installer->getTable('review_status'), $bind);
}

$data = array(
    \Magento\Review\Model\Rating::ENTITY_PRODUCT_CODE => array(
        array('rating_code' => 'Quality', 'position' => 0),
        array('rating_code' => 'Value', 'position' => 0),
        array('rating_code' => 'Price', 'position' => 0)
    ),
    \Magento\Review\Model\Rating::ENTITY_PRODUCT_REVIEW_CODE => array(),
    \Magento\Review\Model\Rating::ENTITY_REVIEW_CODE => array()
);

foreach ($data as $entityCode => $ratings) {
    //Fill table rating/rating_entity
    $installer->getConnection()->insert($installer->getTable('rating_entity'), array('entity_code' => $entityCode));
    $entityId = $installer->getConnection()->lastInsertId($installer->getTable('rating_entity'));

    foreach ($ratings as $bind) {
        //Fill table rating/rating
        $bind['entity_id'] = $entityId;
        $installer->getConnection()->insert($installer->getTable('rating'), $bind);

        //Fill table rating/rating_option
        $ratingId = $installer->getConnection()->lastInsertId($installer->getTable('rating'));
        $optionData = array();
        for ($i = 1; $i <= 5; $i++) {
            $optionData[] = array('rating_id' => $ratingId, 'code' => (string)$i, 'value' => $i, 'position' => $i);
        }
        $installer->getConnection()->insertMultiple($installer->getTable('rating_option'), $optionData);
    }
}

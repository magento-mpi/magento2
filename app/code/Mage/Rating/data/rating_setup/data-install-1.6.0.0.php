<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating data install
 *
 * @category    Mage
 * @package     Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$data = array(
    Mage_Rating_Model_Rating::ENTITY_PRODUCT_CODE       => array(
        array(
            'rating_code'   => 'Quality',
            'position'      => 0
        ),
        array(
            'rating_code'   => 'Value',
            'position'      => 0
        ),
        array(
            'rating_code'   => 'Price',
            'position'      => 0
        ),
    ),
    Mage_Rating_Model_Rating::ENTITY_PRODUCT_REVIEW_CODE    => array(
    ),
    Mage_Rating_Model_Rating::ENTITY_REVIEW_CODE            => array(
    ),
);

foreach ($data as $entityCode => $ratings) {
    //Fill table rating/rating_entity
    $installer->getConnection()
        ->insert($installer->getTable('rating_entity'), array('entity_code' => $entityCode));
    $entityId = $installer->getConnection()->lastInsertId($installer->getTable('rating_entity'));

    foreach ($ratings as $bind) {
        //Fill table rating/rating
        $bind['entity_id'] = $entityId;
        $installer->getConnection()->insert($installer->getTable('rating'), $bind);

        //Fill table rating/rating_option
        $ratingId = $installer->getConnection()->lastInsertId($installer->getTable('rating'));
        $optionData = array();
        for ($i = 1; $i <= 5; $i ++) {
            $optionData[] = array(
                'rating_id' => $ratingId,
                'code'      => (string)$i,
                'value'     => $i,
                'position'  => $i
            );
        }
        $installer->getConnection()->insertMultiple($installer->getTable('rating_option'), $optionData);
    }
}
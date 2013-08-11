<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('admin_rule');

if ($tableName) {
    /** @var Magento_DB_Adapter_Interface $connection */
    $connection = $installer->getConnection();
    $remove = array(
        'Magento_Catalog::catalog_attributes',
        'Mage_Cms::cms',
        'Mage_Newsletter::admin_newsletter',
        'Mage_Review::pending',
        'Mage_Review::reviews',
        'Mage_Review::reviews_ratings',
        'Mage_Tag::tag',
        'Mage_Tag::tag_pending',
    );
    $connection->delete($tableName, array('resource_id IN (?)' => $remove));
}

$installer->endSetup();
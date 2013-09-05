<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('admin_rule');

if ($tableName) {
    /** @var \Magento\DB\Adapter\AdapterInterface $connection */
    $connection = $installer->getConnection();
    $remove = array(
        'Magento_Catalog::catalog_attributes',
        'Magento_Cms::cms',
        'Magento_Newsletter::admin_newsletter',
        'Magento_Review::pending',
        'Magento_Review::reviews',
        'Magento_Review::reviews_ratings',
    );
    $connection->delete($tableName, array('resource_id IN (?)' => $remove));
}

$installer->endSetup();
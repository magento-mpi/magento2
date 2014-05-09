<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Framework\Module\Setup */
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('admin_rule');

if ($tableName) {
    /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
    $connection = $installer->getConnection();
    $remove = array(
        'Magento_Catalog::catalog_attributes',
        'Magento_Cms::cms',
        'Magento_Newsletter::admin_newsletter',
        'Magento_Review::pending',
        'Magento_Review::reviews',
        'Magento_Review::reviews_ratings'
    );
    $connection->delete($tableName, array('resource_id IN (?)' => $remove));
}

$installer->endSetup();

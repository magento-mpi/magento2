<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Framework\Module\Setup */
$installer = $this;

$installer->startSetup();

$tableName = \Magento\CatalogUrlRewrite\Model\Resource\Category\Product::TABLE_NAME;
if ($installer->getConnection()->isTableExists($installer->getTable('url_rewrite_relation'))) {
    $installer->getConnection()
        ->renameTable($installer->getTable('url_rewrite_relation'), $installer->getTable($tableName));
}

$installer->endSetup();

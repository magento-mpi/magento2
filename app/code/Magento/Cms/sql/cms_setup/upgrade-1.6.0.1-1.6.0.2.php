<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$table = $installer->getConnection()->newTable(
    $installer->getTable('cms_url_rewrite')
)->addColumn(
    'cms_rewrite_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Cms Url Rewrite ID'
)->addColumn(
    'url_rewrite_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Core Url Rewrite ID'
)->addIndex(
    $installer->getIdxName('cms_url_rewrite', array('url_rewrite_id')),
    array('url_rewrite_id')
)->addForeignKey(
    $installer->getFkName('cms_url_rewrite', 'url_rewrite_id', 'core_url_rewrite', 'url_rewrite_id'),
    'url_rewrite_id',
    $installer->getTable('core_url_rewrite'),
    'url_rewrite_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addColumn(
    'cms_page_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false),
    'Cms Page ID'
)->addIndex(
    $installer->getIdxName('cms_url_rewrite', array('cms_page_id')),
    array('cms_page_id')
)->addForeignKey(
    $installer->getFkName('cms_url_rewrite', 'cms_page_id', 'cms_page', 'page_id'),
    'cms_page_id',
    $installer->getTable('cms_page'),
    'page_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Cms Url Rewrite Table'
);
$installer->getConnection()->createTable($table);

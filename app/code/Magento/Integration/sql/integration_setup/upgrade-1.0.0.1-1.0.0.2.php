<?php
/**
 * Upgrade script for integration table.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->getConnection()->addColumn(
    $installer->getTable('integration'),
    'identity_link_url',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 255, 'comment' => 'Identity linking Url')
);

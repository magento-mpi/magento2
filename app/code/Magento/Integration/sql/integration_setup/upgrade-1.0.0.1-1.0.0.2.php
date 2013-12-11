<?php
/**
 * Upgrade script for integration table.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var \Magento\Integration\Model\Resource\Setup $installer */
$installer = $this;
$installer->getConnection()->addColumn(
    $installer->getTable('integration'),
    'identity_link_url',
    array(
        'type' => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Identity linking Url'
    )
);
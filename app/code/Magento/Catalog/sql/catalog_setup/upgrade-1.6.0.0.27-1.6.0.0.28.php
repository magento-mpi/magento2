<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento\Setup\Module\SetupModule */
$this->getConnection()->addColumn(
    $this->getTable('catalog_eav_attribute'),
    'is_required_in_admin_store',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Is Required In Admin Store'
    )
);

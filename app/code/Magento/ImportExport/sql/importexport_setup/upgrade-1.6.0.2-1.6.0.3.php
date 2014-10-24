<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('importexport_importdata'),
    'entity_subtype',
    array(
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'LENGTH' => 50,
        'COMMENT' => 'Defines entity subtype to have ability import entity data partially'
    )
);

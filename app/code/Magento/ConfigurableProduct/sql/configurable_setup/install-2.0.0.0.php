<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer \Magento\Catalog\Model\Resource\Setup */

$installer->startSetup();

$table = $installer->getConnection()->addColumn(
    $installer->getTable('catalog_eav_attribute'),
    'is_configurable',
    array(
        'type'      => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    )
);

$installer->endSetup();

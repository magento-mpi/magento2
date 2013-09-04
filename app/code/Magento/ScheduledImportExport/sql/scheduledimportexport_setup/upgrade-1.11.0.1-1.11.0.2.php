<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_ImportExport_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()
    ->addColumn(
        $installer->getTable('magento_scheduled_operations'),
        'entity_subtype',
        array(
            'type'     => \Magento\DB\Ddl\Table::TYPE_TEXT,
            'length'   => 50,
            'comment'  => 'Sub Entity',
            'nullable' => true
        )
    );

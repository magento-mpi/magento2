<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()
        ->modifyColumn($installer->getTable('magento_scheduled_operations'), 'force_import', array(
            'type'     => Magento_DB_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'default'  => '0'
        ));

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_ImportExport_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()
        ->modifyColumn($installer->getTable('enterprise_scheduled_operations'), 'force_import', array(
            'type'     => Magento_DB_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'default'  => '0'
        ));

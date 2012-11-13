<?php
/**
 * Update script for Webapi module.
 *
 * @copyright  {}
 */

/* @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$table = $installer->getTable('webapi_user');
$connection->addColumn(
    $table,
    'api_secret',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'comment' => 'API Secret used for authentication.',
    )
);

$installer->endSetup();

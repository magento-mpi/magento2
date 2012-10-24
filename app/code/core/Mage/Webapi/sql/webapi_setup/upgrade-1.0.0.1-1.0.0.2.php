<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('webapi_user');

$connection->addColumn($table, 'company_name', array(
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'nullable' => true,
    'comment'  => 'Company Name',
));
$connection->addColumn($table, 'contact_email', array(
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'nullable' => false,
    'comment'  => 'Contact Email',
));
$connection->changeColumn(
    $table,
    'user_name',
    'api_key',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'nullable' => false,
        'comment'   => 'Web API key'
    )
);

$installer->endSetup();

<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'job_queue'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('job_queue'))
    ->addColumn('unique_key', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        'primary'   => true,
    ), 'Unique task key')
    ->addColumn('handle', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Job handle')
    ->setComment('Job Queue table');
$installer->getConnection()->createTable($table);

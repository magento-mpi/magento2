<?php
/**
 * {license_notice}
 *
 * @category    Social
 * @package     Social_Facebook
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Social_Facebook_Model_Resource_Setup */
$installer = $this;

/**
 * Create table 'social_facebook_actions'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('social_facebook_actions'))
    ->addColumn('entity_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
    ->addColumn('facebook_id', Magento_DB_Ddl_Table::TYPE_TEXT, 100, array(
        'nullable'  => false,
        ), 'Facebook User Id')
    ->addColumn('facebook_name', Magento_DB_Ddl_Table::TYPE_TEXT, 100, array(
        'nullable'  => false,
        ), 'Facebook User Name')
    ->addColumn('facebook_action', Magento_DB_Ddl_Table::TYPE_SMALLINT, 5, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'User Action')
    ->addColumn('item_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Product Id')
    ->setComment('Social Facebook Actions');
$installer->getConnection()->createTable($table);

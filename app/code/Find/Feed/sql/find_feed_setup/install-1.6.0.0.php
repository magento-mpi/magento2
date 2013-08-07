<?php
/**
 * {license_notice}
 *
 * @category    Find
 * @package     Find_Feed
 * @copyright   {copyright}
 * @license     {license_link}
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
/**
 * Create table 'find_feed_import_codes'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('find_feed_import_codes'))
    ->addColumn('code_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Code id')
    ->addColumn('import_code', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Import type')
    ->addColumn('eav_code', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'EAV code')
    ->addColumn('is_imported', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Is imported')
    ->setComment('Find feed import codes');
$installer->getConnection()->createTable($table);

$this->addAttribute('catalog_product', 'is_imported', array(
    'group'                    => 'General',
    'type'                     => 'int',
    'input'                    => 'select',
    'label'                    => 'In feed',
    'global'                   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'                  => 1,
    'required'                 => 0,
    'visible_on_front'         => 0,
    'is_html_allowed_on_front' => 0,
    'is_configurable'          => 0,
    'source'                   => 'Mage_Eav_Model_Entity_Attribute_Source_Boolean',
    'searchable'               => 0,
    'filterable'               => 0,
    'comparable'               => 0,
    'unique'                   => false,
    'user_defined'             => false,
    'is_user_defined'          => false,
    'used_in_product_listing'  => true
));

$installer->endSetup();
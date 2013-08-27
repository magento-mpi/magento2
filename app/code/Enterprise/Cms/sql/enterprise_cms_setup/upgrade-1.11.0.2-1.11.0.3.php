<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()
    ->addColumn(
        $installer->getTable('enterprise_cms_hierarchy_metadata'),
        'top_menu_visibility',
        array(
            'type'     => Magento_DB_Ddl_Table::TYPE_SMALLINT,
            'comment'  => 'Top Menu Visibility',
            'nullable' => true,
            'default'  => null,
            'unsigned' => true,
        )
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('enterprise_cms_hierarchy_metadata'),
        'top_menu_excluded',
        array(
            'type'     => Magento_DB_Ddl_Table::TYPE_SMALLINT,
            'comment'  => 'Top Menu Excluded',
            'nullable' => true,
            'default'  => null,
            'unsigned' => true,
        )
    );

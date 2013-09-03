<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()
    ->addColumn(
        $installer->getTable('magento_versionscms_hierarchy_metadata'),
        'top_menu_visibility',
        array(
            'type'     => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
            'comment'  => 'Top Menu Visibility',
            'nullable' => true,
            'default'  => null,
            'unsigned' => true,
        )
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('magento_versionscms_hierarchy_metadata'),
        'top_menu_excluded',
        array(
            'type'     => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
            'comment'  => 'Top Menu Excluded',
            'nullable' => true,
            'default'  => null,
            'unsigned' => true,
        )
    );

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
$installer->startSetup();

$nodeTableName = $installer->getTable('magento_versionscms_hierarchy_node');

$installer
    ->getConnection()
    ->dropIndex($nodeTableName, $installer->getIdxName(
        'magento_versionscms_hierarchy_node',
        array('request_url'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
    );

$keyFieldsList = array('request_url', 'scope', 'scope_id');
$installer
    ->getConnection()
    ->addIndex(
        $nodeTableName,
        $installer->getIdxName(
            'magento_versionscms_hierarchy_node',
            $keyFieldsList,
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        $keyFieldsList,
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    );

$installer->endSetup();

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$nodeTableName = $installer->getTable('enterprise_cms_hierarchy_node');

$installer
    ->getConnection()
    ->dropIndex($nodeTableName, $installer->getIdxName(
        'enterprise_cms_hierarchy_node',
        array('request_url'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    );

$keyFieldsList = array('request_url', 'scope', 'scope_id');
$installer
    ->getConnection()
    ->addIndex(
        $nodeTableName,
        $installer->getIdxName(
            'enterprise_cms_hierarchy_node',
            $keyFieldsList,
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        $keyFieldsList,
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );

$installer->endSetup();

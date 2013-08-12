<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Enterprise_TargetRule_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$connection->modifyColumn(
        $installer->getTable('enterprise_targetrule'),
        'use_customer_segment',
        array(
            'type'     => Magento_DB_Ddl_Table::TYPE_SMALLINT,
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            'comment'  => 'Deprecated after 1.11.2.0'
        )
);

$connection->modifyColumn(
        $installer->getTable('enterprise_targetrule_product'),
        'store_id',
        array(
            'type'      => Magento_DB_Ddl_Table::TYPE_SMALLINT,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            'comment'   => 'Deprecated after 1.11.2.0'
        )
);

$installer->endSetup();

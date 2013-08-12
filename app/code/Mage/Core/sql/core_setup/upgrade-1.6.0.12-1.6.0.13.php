<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('core_theme'),
    'code',
    array(
        'type'    => Magento_DB_Ddl_Table::TYPE_TEXT,
        'comment' => 'Full theme code, including package'
    )
);

$installer->endSetup();

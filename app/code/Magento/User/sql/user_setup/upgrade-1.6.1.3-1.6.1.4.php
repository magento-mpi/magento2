<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

$connection->addColumn($installer->getTable('admin_user'), 'interface_locale', array(
    'type'     => Magento_DB_Ddl_Table::TYPE_TEXT,
    'length'   => 5,
    'nullable' => false,
    'default'  => Magento_Core_Model_LocaleInterface::DEFAULT_LOCALE,
    'comment'  => 'Backend interface locale'
));
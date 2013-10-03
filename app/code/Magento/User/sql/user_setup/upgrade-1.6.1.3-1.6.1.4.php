<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

$connection->addColumn($installer->getTable('admin_user'), 'interface_locale', array(
    'type'     => \Magento\DB\Ddl\Table::TYPE_TEXT,
    'length'   => 5,
    'nullable' => false,
    'default'  => \Magento\Core\Model\LocaleInterface::DEFAULT_LOCALE,
    'comment'  => 'Backend interface locale'
));
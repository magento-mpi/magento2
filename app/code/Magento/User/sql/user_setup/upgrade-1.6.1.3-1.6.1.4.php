<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Module\Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

$connection->addColumn(
    $installer->getTable('admin_user'),
    'interface_locale',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 5,
        'nullable' => false,
        'default' => \Magento\Locale\ResolverInterface::DEFAULT_LOCALE,
        'comment' => 'Backend interface locale'
    )
);

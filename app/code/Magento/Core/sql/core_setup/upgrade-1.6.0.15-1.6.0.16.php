<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'mview_state'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('mview_state'))
    ->addColumn('state_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'View State Id')
    ->addColumn('view_id', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
    ), 'View Id')
    ->addColumn('mode', \Magento\DB\Ddl\Table::TYPE_TEXT, 16, array(
        'default' => \Magento\Mview\View\StateInterface::MODE_DISABLED,
    ), 'View Mode')
    ->addColumn('status', \Magento\DB\Ddl\Table::TYPE_TEXT, 16, array(
        'default' => \Magento\Mview\View\StateInterface::STATUS_IDLE,
    ), 'View Status')
    ->addColumn('updated', \Magento\DB\Ddl\Table::TYPE_DATETIME, null, array(
    ), 'View updated time')
    ->addColumn('version_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
    ), 'View Version Id')
    ->addIndex($installer->getIdxName('mview_state', array('view_id')),
        array('view_id'))
    ->addIndex($installer->getIdxName('mview_state', array('mode')),
        array('mode'))
    ->setComment('View State');
$installer->getConnection()->createTable($table);

$installer->endSetup();

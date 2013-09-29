<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->dropIndex($installer->getTable('oauth_nonce'), $installer->getIdxName(
        'oauth_nonce',
        array('nonce'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ));

$installer
    ->getConnection()
    ->addColumn(
        $installer->getTable('oauth_nonce'),
        'consumer_id',
        array(
            'type' => \Magento\DB\Ddl\Table::TYPE_INTEGER,
            'unsigned' => true,
            'nullable' => false,
            'comment' => 'Consumer ID'
        ));

$keyFieldsList = array('nonce', 'consumer_id');
$installer
    ->getConnection()
    ->addIndex(
        $installer->getTable('oauth_nonce'),
        $installer->getIdxName(
            'oauth_nonce',
            $keyFieldsList,
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        $keyFieldsList,
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    );

$installer
    ->getConnection()
    ->addForeignKey(
        $installer->getFkName('oauth_nonce', 'consumer_id', 'oauth_consumer', 'entity_id'),
        $installer->getTable('oauth_nonce'),
        'consumer_id',
        $installer->getTable('oauth_consumer'),
        'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\DB\Ddl\Table::ACTION_CASCADE
    );

$installer
    ->getConnection()
    ->addColumn(
        $installer->getTable('oauth_consumer'),
        'http_post_url',
        array(
            'type' => \Magento\DB\Ddl\Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => false,
            'comment' => 'Http Post URL'
        )
    );

$installer->endSetup();

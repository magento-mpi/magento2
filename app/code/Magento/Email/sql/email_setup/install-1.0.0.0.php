<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

/**
 * Create table 'email_template'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('email_template'))
    ->addColumn(
        'template_id',
        \Magento\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Template Id'
    )
    ->addColumn(
        'template_code',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        150,
        array(
            'nullable' => false
        ),
        'Template Name'
    )
    ->addColumn(
        'template_text',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        '64k',
        array(
            'nullable' => false
        ),
        'Template Content'
    )
    ->addColumn(
        'template_styles',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        '64k',
        array(),
        'Templste Styles'
    )
    ->addColumn(
        'template_type',
        \Magento\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
        ),
        'Template Type'
    )
    ->addColumn(
        'template_subject',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        200,
        array(
            'nullable' => false,
        ),
        'Template Subject'
    )
    ->addColumn(
        'template_sender_name',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        200,
        array(),
        'Template Sender Name'
    )
    ->addColumn(
        'template_sender_email',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        200,
        array(),
        'Template Sender Email'
    )
    ->addColumn(
        'added_at',
        \Magento\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Date of Template Creation'
    )
    ->addColumn(
        'modified_at',
        \Magento\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Date of Template Modification'
    )
    ->addColumn(
        'orig_template_code',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        200,
        array(),
        'Original Template Code'
    )
    ->addColumn(
        'orig_template_variables',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        '64k',
        array(),
        'Original Template Variables'
    )
    ->addIndex(
        $installer->getIdxName(
            'email_template',
            array('template_code'),
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('template_code'),
        array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName('email_template', array('added_at')),
        array('added_at')
    )
    ->addIndex(
        $installer->getIdxName('email_template', array('modified_at')),
        array('modified_at')
    )
    ->setComment('Email Templates');

$installer->getConnection()->createTable($table);

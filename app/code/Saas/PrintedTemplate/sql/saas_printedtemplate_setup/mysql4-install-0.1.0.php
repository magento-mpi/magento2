<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$connection = $installer->getConnection();

$templateTable = new Magento_DB_Ddl_Table;
$templateTable->setName($this->getTable('saas_printed_template'))
    ->addColumn(
        'template_id', Magento_DB_Ddl_Table::TYPE_INTEGER, 10,
        array(
            'nullable' => false,
            'unsigned' => true,
            'primary'  => true
        )
    )
    ->addColumn('name', Magento_DB_Ddl_Table::TYPE_TEXT, 255)
    ->addColumn('page_size', Magento_DB_Ddl_Table::TYPE_TEXT, 40)
    ->setOption('ENGINE', 'InnoDB')
    ->setOption('DEFAULT CHARSET', 'utf8')
    ->setOption('COMMENT', 'Printed templates');

$connection->createTable($templateTable);
$connection->addColumn(
    $templateTable->getName(),
    'entity_type',
    'ENUM("invoice","creditmemo","shipment") NOT NULL'
);

$connection->addColumn(
    $templateTable->getName(),
    'page_orientation',
    'ENUM("portrait","landscape")'
);
$connection->addColumn(
    $templateTable->getName(),
    'content',
    'TEXT'
);
$connection->addColumn(
    $templateTable->getName(),
    'created_at',
    'DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00"'
);
$connection->addColumn(
    $templateTable->getName(),
    'updated_at',
    'DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00"'
);
$connection->modifyColumn(
    $templateTable->getName(),
    'template_id',
    'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST'
);

$connection->addColumn(
    $templateTable->getName(),
    'header',
    "text COMMENT 'Template header'"
);

$connection->addColumn(
    $templateTable->getName(),
    'footer',
    "text COMMENT 'Template footer'"
);

$connection->addColumn(
    $templateTable->getName(),
    'header_height',
    "DECIMAL(12,4) COMMENT 'header height'"
);

$connection->addColumn(
    $templateTable->getName(),
    'header_height_measurement',
    "VARCHAR(255) COMMENT 'header height measurement'"
);

$connection->addColumn(
    $templateTable->getName(),
    'footer_height',
    "DECIMAL(12,4) COMMENT 'footer height'"
);

$connection->addColumn(
    $templateTable->getName(),
    'footer_height_measurement',
    "text COMMENT 'footer height measurement'"
);

$connection->addColumn(
    $templateTable->getName(),
    'header_auto_height',
    "INT(1) COMMENT 'calculate header height automaticaly or use manual setup'"
);

$connection->addColumn(
    $templateTable->getName(),
    'footer_auto_height',
    "INT(1) COMMENT 'calculate footer height automaticaly or use manual setup'"
);

$connection->addIndex(
    $templateTable->getName(),
    'INDEX_SAAS_PRINTED_TEMPLATE_ENTITY_TYPE',
    'entity_type'
);

// setup tax order item table
$itemTaxTable = new Magento_DB_Ddl_Table;
$itemTaxTable->setName($this->getTable('saas_printed_template_order_item_tax'))
    ->addColumn('code', Magento_DB_Ddl_Table::TYPE_TEXT, 255)
    ->addColumn('title', Magento_DB_Ddl_Table::TYPE_TEXT, 255)
    ->addColumn('is_tax_after_discount', Magento_DB_Ddl_Table::TYPE_BOOLEAN)
    ->addColumn('is_discount_on_incl_tax', Magento_DB_Ddl_Table::TYPE_BOOLEAN)
    ->addColumn(
        'item_tax_id', Magento_DB_Ddl_Table::TYPE_INTEGER, 10,
        array(
            'nullable' => false,
            'unsigned' => true,
            'primary' => true
        )
    )
    ->addColumn(
        'item_id', Magento_DB_Ddl_Table::TYPE_INTEGER, 10,
        array(
            'nullable' => false,
            'unsigned' => true
        )
    )
    ->addColumn(
        'percent', Magento_DB_Ddl_Table::TYPE_DECIMAL,
        array(12, 4), array('nullable' => false)
    )
    ->addColumn(
        'real_percent', Magento_DB_Ddl_Table::TYPE_DECIMAL,
        array(12, 4), array('nullable' => false)
    )
    ->addColumn(
        'priority', Magento_DB_Ddl_Table::TYPE_INTEGER,
        11, array('nullable' => false)
    )
    ->addColumn(
        'position', Magento_DB_Ddl_Table::TYPE_INTEGER,
        11, array('nullable' => false)
    )
    ->setOption('ENGINE', 'InnoDB')
    ->setOption('DEFAULT CHARSET', 'utf8')
    ->setOption('COMMENT', 'Tax Detailed information for order items')
    ->addIndex('INDEX_SALES_ORDER_ITEM_TAX_ITEM_ID', array('item_id'))
    ->addForeignKey(
        'FK_SALES_ORDER_ITEM_TAX_ITEM_ID',
        'item_id',
        $this->getTable('sales_flat_order_item'),
        'item_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE,
        Magento_DB_Ddl_Table::ACTION_CASCADE
    );

// setup tax order shipping table
$shippingTaxTable = new Magento_DB_Ddl_Table;
$shippingTaxTable->setName(
    $this->getTable('saas_printed_template_order_shipping_tax')
)
    ->addColumn('code', Magento_DB_Ddl_Table::TYPE_TEXT, 255)
    ->addColumn('title', Magento_DB_Ddl_Table::TYPE_TEXT, 255)
    ->addColumn('is_tax_after_discount', Magento_DB_Ddl_Table::TYPE_BOOLEAN)
    ->addColumn('is_discount_on_incl_tax', Magento_DB_Ddl_Table::TYPE_BOOLEAN)
    ->addColumn(
        'shipping_tax_id', Magento_DB_Ddl_Table::TYPE_INTEGER, 10,
        array(
            'nullable' => false,
            'unsigned' => true,
            'primary'  => true
        )
    )
    ->addColumn(
        'order_id', Magento_DB_Ddl_Table::TYPE_INTEGER, 10,
        array(
            'nullable' => false,
            'unsigned' => true
        )
    )
    ->addColumn(
        'percent', Magento_DB_Ddl_Table::TYPE_DECIMAL,
        array(12, 4), array('nullable' => false)
    )
    ->addColumn(
        'real_percent', Magento_DB_Ddl_Table::TYPE_DECIMAL,
        array(12, 4), array('nullable' => false)
    )
    ->addColumn(
        'priority', Magento_DB_Ddl_Table::TYPE_INTEGER, 11,
        array('nullable' => false)
    )
    ->addColumn(
        'position', Magento_DB_Ddl_Table::TYPE_INTEGER,
        11, array('nullable' => false)
    )
    ->setOption('ENGINE', 'InnoDB')
    ->setOption('DEFAULT CHARSET', 'utf8')
    ->setOption('COMMENT', 'Tax detailed information for shipping method')
    ->addIndex('INDEX_SALES_ORDER_ITEM_SHIPPING_ORDER_ID', array('order_id'))
    ->addForeignKey(
        'FK_SALES_ORDER_SHIPPING_TAX_ORDER_ID',
        'order_id',
        $this->getTable('sales_flat_order'),
        'entity_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE,
        Magento_DB_Ddl_Table::ACTION_CASCADE
    );

$connection->createTable($itemTaxTable);
$connection->modifyColumn(
    $itemTaxTable->getName(),
    'item_tax_id',
    'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST'
);

$connection->createTable($shippingTaxTable);
$connection->modifyColumn(
    $shippingTaxTable->getName(),
    'shipping_tax_id',
    'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST'
);

$installer->endSetup();

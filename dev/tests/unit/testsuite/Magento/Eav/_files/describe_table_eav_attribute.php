<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

return [
    'attribute_id' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'attribute_id',
        'COLUMN_POSITION' => 1,
        'DATA_TYPE' => 'smallint',
        'DEFAULT' => null,
        'NULLABLE' => false,
        'LENGTH' => null,
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => true,
        'PRIMARY' => true,
        'PRIMARY_POSITION' => 1,
        'IDENTITY' => true,
    ],
    'entity_type_id' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'entity_type_id',
        'COLUMN_POSITION' => 2,
        'DATA_TYPE' => 'smallint',
        'DEFAULT' => '0',
        'NULLABLE' => false,
        'LENGTH' => null,
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => true,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'attribute_code' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'attribute_code',
        'COLUMN_POSITION' => 3,
        'DATA_TYPE' => 'varchar',
        'DEFAULT' => null,
        'NULLABLE' => true,
        'LENGTH' => '255',
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => null,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'attribute_model' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'attribute_model',
        'COLUMN_POSITION' => 4,
        'DATA_TYPE' => 'varchar',
        'DEFAULT' => null,
        'NULLABLE' => true,
        'LENGTH' => '255',
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => null,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'backend_model' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'backend_model',
        'COLUMN_POSITION' => 5,
        'DATA_TYPE' => 'varchar',
        'DEFAULT' => null,
        'NULLABLE' => true,
        'LENGTH' => '255',
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => null,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'backend_type' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'backend_type',
        'COLUMN_POSITION' => 6,
        'DATA_TYPE' => 'varchar',
        'DEFAULT' => 'static',
        'NULLABLE' => false,
        'LENGTH' => '8',
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => null,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'backend_table' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'backend_table',
        'COLUMN_POSITION' => 7,
        'DATA_TYPE' => 'varchar',
        'DEFAULT' => null,
        'NULLABLE' => true,
        'LENGTH' => '255',
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => null,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'frontend_model' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'frontend_model',
        'COLUMN_POSITION' => 8,
        'DATA_TYPE' => 'varchar',
        'DEFAULT' => null,
        'NULLABLE' => true,
        'LENGTH' => '255',
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => null,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'frontend_input' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'frontend_input',
        'COLUMN_POSITION' => 9,
        'DATA_TYPE' => 'varchar',
        'DEFAULT' => null,
        'NULLABLE' => true,
        'LENGTH' => '50',
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => null,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'frontend_label' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'frontend_label',
        'COLUMN_POSITION' => 10,
        'DATA_TYPE' => 'varchar',
        'DEFAULT' => null,
        'NULLABLE' => true,
        'LENGTH' => '255',
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => null,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'frontend_class' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'frontend_class',
        'COLUMN_POSITION' => 11,
        'DATA_TYPE' => 'varchar',
        'DEFAULT' => null,
        'NULLABLE' => true,
        'LENGTH' => '255',
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => null,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'source_model' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'source_model',
        'COLUMN_POSITION' => 12,
        'DATA_TYPE' => 'varchar',
        'DEFAULT' => null,
        'NULLABLE' => true,
        'LENGTH' => '255',
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => null,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'is_required' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'is_required',
        'COLUMN_POSITION' => 13,
        'DATA_TYPE' => 'smallint',
        'DEFAULT' => '0',
        'NULLABLE' => false,
        'LENGTH' => null,
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => true,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'is_user_defined' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'is_user_defined',
        'COLUMN_POSITION' => 14,
        'DATA_TYPE' => 'smallint',
        'DEFAULT' => '0',
        'NULLABLE' => false,
        'LENGTH' => null,
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => true,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'default_value' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'default_value',
        'COLUMN_POSITION' => 15,
        'DATA_TYPE' => 'text',
        'DEFAULT' => null,
        'NULLABLE' => true,
        'LENGTH' => null,
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => null,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'is_unique' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'is_unique',
        'COLUMN_POSITION' => 16,
        'DATA_TYPE' => 'smallint',
        'DEFAULT' => '0',
        'NULLABLE' => false,
        'LENGTH' => null,
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => true,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ],
    'note' => [
        'SCHEMA_NAME' => null,
        'TABLE_NAME' => 'eav_attribute',
        'COLUMN_NAME' => 'note',
        'COLUMN_POSITION' => 17,
        'DATA_TYPE' => 'varchar',
        'DEFAULT' => null,
        'NULLABLE' => true,
        'LENGTH' => '255',
        'SCALE' => null,
        'PRECISION' => null,
        'UNSIGNED' => null,
        'PRIMARY' => false,
        'PRIMARY_POSITION' => null,
        'IDENTITY' => false,
    ]
];

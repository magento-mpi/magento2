<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    '$replaceRules' => array(
        array(
            'table',
            'collection',
            Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_RESOURCE,
            Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
            array('pk_field1', 'pk_field2')
        )
    ),
    '$tableData' => array(
        array('collection' => 'customer/attribute_collection', 'pk_field1' => 'pk_value1', 'pk_field2' => 'pk_value2'),
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'collection',
                'to'    => 'Magento_Customer_Model_Resource_Attribute_Collection',
                'from'  => array('`pk_field1` = ?' => 'pk_value1', '`pk_field2` = ?' => 'pk_value2')
            ),
        ),
        'aliases_map' => array(
            Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_RESOURCE => array(
                'customer/attribute_collection' => 'Magento_Customer_Model_Resource_Attribute_Collection'
            ),
        )
    ),
);

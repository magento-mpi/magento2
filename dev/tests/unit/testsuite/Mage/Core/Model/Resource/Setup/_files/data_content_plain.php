<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    '$replaceRules' => array(
        array(
            'table',
            'field',
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
            Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
            ''
        ),
        array(
            'table',
            'collection',
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_RESOURCE,
            Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
            'flag = 1'
        )
    ),
    '$tableData' => array(
        array('field'      => 'customer/customer'),
        array('field'      => 'customer/attribute_data_postcode'),
        array('field'      => 'customer/attribute_data_postcode::someMethod'),
        array('field'      => 'Mage_Customer_Model_Customer'),
        array('collection' => 'customer/attribute_collection'),
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'Mage_Customer_Model_Customer_FROM_MAP',
                'from'  => 'customer/customer'
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'Mage_Customer_Model_Attribute_Data_Postcode',
                'from'  => 'customer/attribute_data_postcode'
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'Mage_Customer_Model_Attribute_Data_Postcode::someMethod',
                'from'  => 'customer/attribute_data_postcode::someMethod'
            ),
            array(
                'table' => 'table',
                'field' => 'collection',
                'to'    => 'Mage_Customer_Model_Resource_Attribute_Collection',
                'from'  => 'customer/attribute_collection'
            ),
        ),
        'where' => array(
            'flag = 1'
        ),
        'aliases_map' => array(
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL => array(
                'customer/customer' => 'Mage_Customer_Model_Customer_FROM_MAP',
                'customer/attribute_data_postcode' => 'Mage_Customer_Model_Attribute_Data_Postcode'
            ),
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_RESOURCE => array(
                'customer/attribute_collection' => 'Mage_Customer_Model_Resource_Attribute_Collection'
            ),
        )
    ),
    '$aliasesMap' => array(
        Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL => array(
            'customer/customer' => 'Mage_Customer_Model_Customer_FROM_MAP'
        )
    )
);

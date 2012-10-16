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
        )
    ),
    '$tableData' => array(
        array('field' => 'customer/customer'),
        array('field' => 'customer/attribute_data_postcode'),
        array('field' => 'customer/attribute_data_postcode::someMethod'),
        array('field' => 'catalogSearch/session'),
        array('field' => 'catalogSearch/session::someMethod'),
        array('field' => 'Mage_Customer_Model_Customer'),
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'Mage_Customer_Model_Customer_FROM_MAP',
                'from'  => array('`field` = ?' => 'customer/customer')
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'Mage_Customer_Model_Attribute_Data_Postcode',
                'from'  => array('`field` = ?' => 'customer/attribute_data_postcode')
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'Mage_Customer_Model_Attribute_Data_Postcode::someMethod',
                'from'  => array('`field` = ?' => 'customer/attribute_data_postcode::someMethod')
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'Mage_CatalogSearch_Model_Session',
                'from'  => array('`field` = ?' => 'catalogSearch/session')
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'Mage_CatalogSearch_Model_Session::someMethod',
                'from'  => array('`field` = ?' => 'catalogSearch/session::someMethod')
            ),
        ),
        'aliases_map' => array(
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL => array(
                'customer/customer'                => 'Mage_Customer_Model_Customer_FROM_MAP',
                'customer/attribute_data_postcode' => 'Mage_Customer_Model_Attribute_Data_Postcode',
                'catalogSearch/session'            => 'Mage_CatalogSearch_Model_Session',
            ),
        )
    ),
    '$aliasesMap' => array(
        Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL => array(
            'customer/customer' => 'Mage_Customer_Model_Customer_FROM_MAP'
        )
    )
);

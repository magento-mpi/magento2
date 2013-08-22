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
            'field',
            Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
            Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
        )
    ),
    '$tableData' => array(
        array('field' => 'customer/customer'),
        array('field' => 'customer/attribute_data_postcode'),
        array('field' => 'customer/attribute_data_postcode::someMethod'),
        array('field' => 'Magento_Customer_Model_Customer'),
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'Magento_Customer_Model_Customer_FROM_MAP',
                'from'  => array('`field` = ?' => 'customer/customer')
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'Magento_Customer_Model_Attribute_Data_Postcode',
                'from'  => array('`field` = ?' => 'customer/attribute_data_postcode')
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'Magento_Customer_Model_Attribute_Data_Postcode::someMethod',
                'from'  => array('`field` = ?' => 'customer/attribute_data_postcode::someMethod')
            ),
        ),
        'aliases_map' => array(
            Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL => array(
                'customer/customer'                => 'Magento_Customer_Model_Customer_FROM_MAP',
                'customer/attribute_data_postcode' => 'Magento_Customer_Model_Attribute_Data_Postcode',
            ),
        )
    ),
    '$aliasesMap' => array(
        Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL => array(
            'customer/customer' => 'Magento_Customer_Model_Customer_FROM_MAP'
        )
    )
);
